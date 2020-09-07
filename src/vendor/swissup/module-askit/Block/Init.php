<?php

namespace Swissup\Askit\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Init extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {

        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return string
     */
    public function getJsConfig()
    {
        $jsConfig = $this->_scopeConfig->getValue('askit', ScopeInterface::SCOPE_STORE);
        unset($jsConfig['email']);
        $jsConfig = $this->serializer->serialize($jsConfig);
        return $jsConfig;
    }
}
