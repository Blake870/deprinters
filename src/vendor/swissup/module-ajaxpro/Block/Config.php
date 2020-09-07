<?php
/**
 * Copyright © 2016-2020 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxpro\Block;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Swissup\Ajaxpro\Helper\Config
     */
    private $configHelper;

    /**
     * @param Context $context
     * @param \Swissup\Ajaxpro\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Swissup\Ajaxpro\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;

        parent::__construct($context, $data);
    }

    /**
     *
     * @return bool
     */
    public function isForceValidation()
    {
        return $this->configHelper->isForceValidation();
    }
}
