<?php

namespace Swissup\Pagespeed\Plugin\Block\Html\Header;

class CriticalCssPlugin
{
    /**
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $config;

    /**
     * @param \Swissup\Pagespeed\Helper\Config $config
     */
    public function __construct(
        \Swissup\Pagespeed\Helper\Config $config
    ) {
        $this->config = $config;
    }

    /**
     *
     * @param  \Magento\Theme\Block\Html\Header\CriticalCss $subject
     * @param  string $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCriticalCssData(
        \Magento\Theme\Block\Html\Header\CriticalCss $subject,
        $result
    ) {
        if ($this->config->isCriticalCssEnable()
            && $this->config->isUseCssCriticalPathEnable()
        ) {
            $styles = $this->config->getDefaultCriticalCss();

            $result .= $styles;
        }

        return $result;
    }
}
