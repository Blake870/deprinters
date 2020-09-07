<?php
namespace Swissup\Askit\Controller\Message;

abstract class SaveAbstract extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Swissup\Askit\Helper\Config
     */
    protected $configHelper;

    /**
     *
     * @var \Swissup\Askit\Model\MessageFactory
     */
    protected $messageFactory;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Askit\Helper\Config $configHelper
     * @param \Swissup\Askit\Model\MessageFactory $messageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Askit\Helper\Config $configHelper,
        \Swissup\Askit\Model\MessageFactory $messageFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->messageFactory = $messageFactory;
    }

    /**
     *
     */
    protected function redirectReferer()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
        // $this->_redirect($this->_redirect->getRedirectUrl());
    }
}
