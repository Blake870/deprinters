<?php

namespace Swissup\Gdpr\Observer;

class SaveClientConsents implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\Gdpr\Helper\Data
     */
    private $helper;

    /**
     * @var \Swissup\Gdpr\Model\ClientConsentFactory
     */
    private $clientConsentFactory;

    /**
     * @var \Swissup\Gdpr\Model\ResourceModel\PersonalDataForm\Collection
     */
    private $forms;

    /**
     * @var \Swissup\Gdpr\Model\ResourceModel\ClientConsent\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Customer visitor
     *
     * @var \Magento\Customer\Model\Visitor
     */
    private $customerVisitor;

    /**
     * @var \Swissup\Gdpr\Model\ClientConsentFactory
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    private $shareConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Swissup\Gdpr\Helper\Data $helper
     * @param \Swissup\Gdpr\Model\ResourceModel\PersonalDataForm\Collection $forms
     * @param \Swissup\Gdpr\Model\ClientConsentFactory $clientConsentFactory
     * @param \Magento\Customer\Model\Visitor $customerVisitor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Config\Share $shareConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Swissup\Gdpr\Helper\Data $helper,
        \Swissup\Gdpr\Model\ClientConsentFactory $clientConsentFactory,
        \Swissup\Gdpr\Model\ResourceModel\PersonalDataForm\Collection $forms,
        \Swissup\Gdpr\Model\ResourceModel\ClientConsent\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Config\Share $shareConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->forms = $forms;
        $this->collectionFactory = $collectionFactory;
        $this->clientConsentFactory = $clientConsentFactory;
        $this->customerVisitor = $customerVisitor;
        $this->customerSession = $customerSession;
        $this->shareConfig = $shareConfig;
        $this->storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();
        if (!$request->isPost() || !$this->helper->isGdprEnabled()) {
            return;
        }

        // All data is already validated in predispatch observer,
        // so, if no consents are received - do nothing.
        $receivedConsents = $request->getParam('swissup_gdpr_consent', []);
        if (!$receivedConsents) {
            return;
        }

        // get form with consents
        $form = $this->forms->getItemByColumnValue(
            'action',
            $request->getFullActionName()
        );

        if (!$form) {
            return;
        }

        // Prepare client_consent data
        if ($this->customerSession->getCustomerId()) {
            $identityField = 'email';
            $identity = $this->customerSession->getCustomer()->getEmail();
        } else {
            $identityField = $form->getClientIdentityField() ?
                $form->getClientIdentityField() : 'email';
            $identity = $request->getParam($identityField, '');
        }

        $data = [
            'client_identity_field' => $identityField,
            'client_identity' => $identity,
            'customer_id' => $this->customerSession->getCustomerId(),
            'visitor_id' => $this->customerVisitor->getId(),
            'form_id' => $form->getId(),
        ];
        if (empty($data['client_identity'])) {
            return;
        }

        // save and update ClientConsents
        $acceptedConsents = $this->getAcceptedConsents(
            $form,
            $identity,
            $identityField
        );
        foreach ($form->getConsents() as $consent) {
            if (!isset($receivedConsents[$consent['html_id']])) {
                continue;
            }

            $value = $receivedConsents[$consent['html_id']];
            $model = $acceptedConsents->getItemByColumnValue(
                'consent_id',
                $consent['html_id']
            );

            if (empty($value)) {
                if ($form->getIsRevokable() && $model) {
                    $model->delete();
                }
                continue;
            }

            if ($model) {
                $model->addData($data)
                    ->setUpdatedAt(new \Zend_Db_Expr('NULL'));
            } else {
                $model = $this->clientConsentFactory->create();
                $model->setData($data)
                    ->setConsentId($consent['html_id']);
            }
            $model->setConsentTitle($consent['title'])->save();
        }
    }

    /**
     * Get the list of previously accepted consents
     *
     * @param  \Swissup\Gdpr\Model\PersonalDataForm $form
     * @param  string $identity
     * @param  string $identityField
     * @return \Swissup\Gdpr\Model\ResourceModel\ClientConsent\Collection
     */
    private function getAcceptedConsents(
        \Swissup\Gdpr\Model\PersonalDataForm $form,
        $identity = null,
        $identityField = 'email'
    ) {
        $acceptedConsents = $this->collectionFactory->create()
            ->addFieldToFilter('form_id', $form->getId());

        if ($this->shareConfig->isWebsiteScope()) {
            $acceptedConsents->addFieldToFilter(
                'website_id',
                $this->storeManager->getWebsite()->getWebsiteId()
            );
        }

        if ($identity) {
            $acceptedConsents
                ->addFieldToFilter('client_identity_field', $identityField)
                ->addFieldToFilter('client_identity', $identity);
        }

        if ($customerId = $this->customerSession->getCustomerId()) {
            $acceptedConsents->addFieldToFilter('customer_id', $customerId);
        } else {
            $acceptedConsents->addFieldToFilter('visitor_id', $this->customerVisitor->getId());
        }

        return $acceptedConsents;
    }
}
