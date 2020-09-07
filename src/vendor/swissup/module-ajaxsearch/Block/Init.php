<?php
/**
 * Copyright © 2016-2020 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxsearch\Block;

use Magento\Checkout\Model\Session as CheckoutSession;

class Init extends \Swissup\Ajaxsearch\Block\Template
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    protected $packageInfo;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Ajaxsearch\Helper\Data $configHelper
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Module\PackageInfo $packageInfo
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Ajaxsearch\Helper\Data $configHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->localeFormat = $localeFormat;
        $this->packageInfo = $packageInfo;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context, $configHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        if ($this->configHelper->isEnabled()) {
            $this->pageConfig->addBodyClass('swissup-ajaxsearch-loading');

            if ($this->configHelper->isFoldedDesignEnabled()) {
                $this->pageConfig->addBodyClass('swissup-ajaxsearch-folded-loading');
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    public function getJsonConfig()
    {
        $package = 'swissup/module-ajaxsearch';
        $module = $this->packageInfo->getModuleName($package);

        $currency = $this->checkoutSession->getQuote()->getQuoteCurrencyCode();
        $priceFormat = $this->localeFormat->getPriceFormat(null, $currency);
        $config = [
            'priceFormat' => $priceFormat,
            'package' => $package,
            'module' => $module,
            'version' => $this->packageInfo->getVersion($module)
        ];
        return $this->serializer->serialize($config);
    }

    /**
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->configHelper->getLimit();
    }

    // /**
    //  *
    //  * @return string
    //  */
    // public function getPlaceholder()
    // {
    //     return $this->configHelper->getPlaceholder();
    // }

    /**
     *
     * @return boolean
     */
    public function isHighligth()
    {
        return $this->configHelper->isHighligth();
    }

    /**
     *
     * @return boolean
     */
    public function isHint()
    {
        return $this->configHelper->isHint();
    }

    /**
     *
     * @return string [json]
     */
    public function getClassNames()
    {
        $classNames = $this->configHelper->getClassNames();

        return $this->serializer->serialize($classNames);
    }
}
