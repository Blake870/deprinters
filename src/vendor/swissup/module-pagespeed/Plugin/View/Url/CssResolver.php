<?php

namespace Swissup\Pagespeed\Plugin\View\Url;

use Swissup\Pagespeed\Helper\Config as ConfigHelper;

class CssResolver
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     *
     * @var \Swissup\Pagespeed\Model\Css\FontDisplay
     */
    private $improver;

    /**
     * @param ConfigHelper $configHelper
     * @param \Swissup\Pagespeed\Model\Css\FontDisplay $improver
     */
    public function __construct(
        ConfigHelper $configHelper,
        \Swissup\Pagespeed\Model\Css\FontDisplay $improver
    ) {
        $this->configHelper = $configHelper;
        $this->improver = $improver;
    }

    /**
     *
     * @param  \Magento\Framework\View\Url\CssResolver
     * @return string
     */
    public function afterRelocateRelativeUrls(
        \Magento\Framework\View\Url\CssResolver $subject,
        $result
    ) {

        $cssContent = $result;

        // add font-display:swap
        // https://css-tricks.com/font-display-masses/
        if ($this->configHelper->isAutoAddFontDisplayForMergedCss()) {
            $cssContent = $this->improver->process($cssContent);
        }

        $cssContent = str_replace(['http://', 'https://'], '//', $cssContent);

        return $cssContent;
    }
}
