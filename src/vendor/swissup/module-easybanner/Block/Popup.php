<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\Easybanner\Block;

use Magento\Framework\View\Element\Template;

class Popup extends Template
{
    /**
     * @var string
     */
    protected $_template = 'popup.phtml';

    /**
     * @param Template\Context $context
     * @param \Swissup\Easybanner\Model\ResourceModel\Banner\CollectionFactory $bannerCollection
     * @param \Magento\Framework\ObjectManagerInterface $_objectManager
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Swissup\Easybanner\Model\ResourceModel\Banner\CollectionFactory $bannerCollection,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_bannerCollection = $bannerCollection;
        $this->_objectManager = $_objectManager;
        $this->_jsonEncoder = $jsonEncoder;

        parent::__construct($context, $data);
    }

    public function getBanners()
    {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $_bannerCollection = $this->_bannerCollection->create();

        $_bannerCollection->getSelect()
            ->where('type in (?)', [2, 3])
            ->where('status = ?', 1);

        return $_bannerCollection->load();
    }

    public function getJsonConditions($conditions)
    {
        return $this->_jsonEncoder->encode($conditions);
    }
}
