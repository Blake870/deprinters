<?php

namespace Swissup\Pagespeed\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Critical extends Field
{
    protected $storeManager;

    /**
     * GettingStarted constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $moduleConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getRequest()->getParam('store')) {
            $storeId = $this->getRequest()->getParam('store');
            $url = $this->storeManager->getStore($storeId)->getBaseUrl();
        } else {
            $url = $this->storeManager->getStore()->getBaseUrl();
        }
        $apiUrl = 'http://ci.swissuplabs.com/pagespeed/critical-css/generate?website=' . urlencode($url);

        return parent::_getElementHtml($element) . '<a href="' . $apiUrl . '" target="_blank" >Get your site critical css.</a>';
    }
}
