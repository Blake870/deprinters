<?php

namespace Swissup\Gdpr\Block\Privacy;

use Magento\Framework\View\Element\Template;

class AcceptedConsents extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Swissup_Gdpr::privacy/accepted-consents.phtml';

    /**
     * @param Template\Context                                                  $context
     * @param \Swissup\Gdpr\Model\ResourceModel\ClientConsent\CollectionFactory $collectionFactory
     * @param \Swissup\Gdpr\Model\ResourceModel\PersonalDataForm\Collection     $forms
     * @param \Magento\Customer\Model\Session                                   $customerSession
     * @param array                                                             $data
     */
    public function __construct(
        Template\Context $context,
        \Swissup\Gdpr\Model\ResourceModel\ClientConsent\CollectionFactory $collectionFactory,
        \Swissup\Gdpr\Model\ResourceModel\PersonalDataForm\Collection $forms,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->forms = $forms;
        $this->customerSession = $customerSession;
    }

    /**
     * Get the list of consents grouped by form_id
     *
     * @return array
     */
    public function getGroupedConsents()
    {
        $result = [];
        $consents = $this->getConsents();
        $formIds = $consents->getColumnValues('form_id');
        $formIds = array_unique($formIds);

        foreach ($formIds as $formId) {
            $form = $this->forms->getItemById($formId);
            if (!$form) {
                continue;
            }

            $result[$form->getShortname()] = $consents->getItemsByColumnValue(
                'form_id', $formId
            );
        }

        return $result;
    }

    /**
     * Get the list of accepted consents
     *
     * @return \Swissup\Gdpr\Model\ResourceModel\ClientConsent\Collection
     */
    public function getConsents()
    {
        $collection = $this->collectionFactory->create();

        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        } else {
            $collection->addFieldToFilter('client_identity', $this->getVerifiedIdentity());
        }

        return $collection;
    }

    /**
     * @todo: implement token verification for the guest customers
     *
     * @return string|boolean
     */
    private function getVerifiedIdentity()
    {
        return false;

        // $token = $this->getRequest()->getParam('token');
        // if ($token) {
        //     return $this->getRequest()->getParam('identity');
        // }
        // return false;
    }
}
