<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Customer\CustomerData\JsLayoutDataProviderInterface;

class OverrideMinicartJsLayoutDataProvider implements JsLayoutDataProviderInterface
{
    /**
     * @var \Swissup\Ajaxpro\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Swissup\Ajaxpro\Helper\Config $configHelper
     */
    public function __construct(
        \Swissup\Ajaxpro\Helper\Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'components' => [
                'minicart_content' => [
                    'config' => [
                        'override_minicart' => $this->configHelper->isOverrideMinicart(),
                    ],
                ],

                'ajaxpro_minicart_content' => [
                    'config' => [
                        'override_minicart' => $this->configHelper->isOverrideMinicart(),
                    ],
                ],
            ],
        ];
    }
}
