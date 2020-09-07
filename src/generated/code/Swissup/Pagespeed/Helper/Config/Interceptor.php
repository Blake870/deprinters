<?php
namespace Swissup\Pagespeed\Helper\Config;

/**
 * Interceptor class for @see \Swissup\Pagespeed\Helper\Config
 */
class Interceptor extends \Swissup\Pagespeed\Helper\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\App\State $state, \Magento\Framework\Filesystem $filesystem, \Swissup\Pagespeed\Model\Css\FontDisplay $cssImprover, \Mobile_Detect $detector)
    {
        $this->___init();
        parent::__construct($context, $state, $filesystem, $cssImprover, $detector);
    }

    /**
     * {@inheritdoc}
     */
    public function isDeveloperMode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeveloperMode');
        if (!$pluginInfo) {
            return parent::isDeveloperMode();
        } else {
            return $this->___callPlugins('isDeveloperMode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeveloperModeIgnored()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeveloperModeIgnored');
        if (!$pluginInfo) {
            return parent::isDeveloperModeIgnored();
        } else {
            return $this->___callPlugins('isDeveloperModeIgnored', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEnable');
        if (!$pluginInfo) {
            return parent::isEnable();
        } else {
            return $this->___callPlugins('isEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isContentMinifyEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isContentMinifyEnable');
        if (!$pluginInfo) {
            return parent::isContentMinifyEnable();
        } else {
            return $this->___callPlugins('isContentMinifyEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isContentJsMinifyEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isContentJsMinifyEnable');
        if (!$pluginInfo) {
            return parent::isContentJsMinifyEnable();
        } else {
            return $this->___callPlugins('isContentJsMinifyEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isContentCssMinifyEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isContentCssMinifyEnable');
        if (!$pluginInfo) {
            return parent::isContentCssMinifyEnable();
        } else {
            return $this->___callPlugins('isContentCssMinifyEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAddExpireEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAddExpireEnable');
        if (!$pluginInfo) {
            return parent::isAddExpireEnable();
        } else {
            return $this->___callPlugins('isAddExpireEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireTTL()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExpireTTL');
        if (!$pluginInfo) {
            return parent::getExpireTTL();
        } else {
            return $this->___callPlugins('getExpireTTL', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDnsPrefetchEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDnsPrefetchEnable');
        if (!$pluginInfo) {
            return parent::isDnsPrefetchEnable();
        } else {
            return $this->___callPlugins('isDnsPrefetchEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isJsMergeEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isJsMergeEnable');
        if (!$pluginInfo) {
            return parent::isJsMergeEnable();
        } else {
            return $this->___callPlugins('isJsMergeEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeferJsEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeferJsEnable');
        if (!$pluginInfo) {
            return parent::isDeferJsEnable();
        } else {
            return $this->___callPlugins('isDeferJsEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeferJsUnpackEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeferJsUnpackEnable');
        if (!$pluginInfo) {
            return parent::isDeferJsUnpackEnable();
        } else {
            return $this->___callPlugins('isDeferJsUnpackEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMergeJsFilesForMobileDisabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMergeJsFilesForMobileDisabled');
        if (!$pluginInfo) {
            return parent::isMergeJsFilesForMobileDisabled();
        } else {
            return $this->___callPlugins('isMergeJsFilesForMobileDisabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeferCssEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeferCssEnable');
        if (!$pluginInfo) {
            return parent::isDeferCssEnable();
        } else {
            return $this->___callPlugins('isDeferCssEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDeferCssOnloadEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDeferCssOnloadEnable');
        if (!$pluginInfo) {
            return parent::isDeferCssOnloadEnable();
        } else {
            return $this->___callPlugins('isDeferCssOnloadEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMobile()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMobile');
        if (!$pluginInfo) {
            return parent::isMobile();
        } else {
            return $this->___callPlugins('isMobile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMergeCssFilesForMobileDisabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMergeCssFilesForMobileDisabled');
        if (!$pluginInfo) {
            return parent::isMergeCssFilesForMobileDisabled();
        } else {
            return $this->___callPlugins('isMergeCssFilesForMobileDisabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoAddFontDisplayForMergedCss()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAutoAddFontDisplayForMergedCss');
        if (!$pluginInfo) {
            return parent::isAutoAddFontDisplayForMergedCss();
        } else {
            return $this->___callPlugins('isAutoAddFontDisplayForMergedCss', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImageLazyLoadEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImageLazyLoadEnable');
        if (!$pluginInfo) {
            return parent::isImageLazyLoadEnable();
        } else {
            return $this->___callPlugins('isImageLazyLoadEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyloadIgnores()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLazyloadIgnores');
        if (!$pluginInfo) {
            return parent::getLazyloadIgnores();
        } else {
            return $this->___callPlugins('getLazyloadIgnores', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImageLazyLoadIgnored($src)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImageLazyLoadIgnored');
        if (!$pluginInfo) {
            return parent::isImageLazyLoadIgnored($src);
        } else {
            return $this->___callPlugins('isImageLazyLoadIgnored', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImageLazyLoadPlaceholderEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImageLazyLoadPlaceholderEnable');
        if (!$pluginInfo) {
            return parent::isImageLazyLoadPlaceholderEnable();
        } else {
            return $this->___callPlugins('isImageLazyLoadPlaceholderEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyloadPlaceholder()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLazyloadPlaceholder');
        if (!$pluginInfo) {
            return parent::getLazyloadPlaceholder();
        } else {
            return $this->___callPlugins('getLazyloadPlaceholder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyloadOffset()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLazyloadOffset');
        if (!$pluginInfo) {
            return parent::getLazyloadOffset();
        } else {
            return $this->___callPlugins('getLazyloadOffset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyloadMobileOffset()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLazyloadMobileOffset');
        if (!$pluginInfo) {
            return parent::getLazyloadMobileOffset();
        } else {
            return $this->___callPlugins('getLazyloadMobileOffset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isUseCssCriticalPathEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isUseCssCriticalPathEnable');
        if (!$pluginInfo) {
            return parent::isUseCssCriticalPathEnable();
        } else {
            return $this->___callPlugins('isUseCssCriticalPathEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCriticalCssEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isCriticalCssEnable');
        if (!$pluginInfo) {
            return parent::isCriticalCssEnable();
        } else {
            return $this->___callPlugins('isCriticalCssEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCriticalCss()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefaultCriticalCss');
        if (!$pluginInfo) {
            return parent::getDefaultCriticalCss();
        } else {
            return $this->___callPlugins('getDefaultCriticalCss', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isDimensionEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDimensionEnable');
        if (!$pluginInfo) {
            return parent::isDimensionEnable();
        } else {
            return $this->___callPlugins('isDimensionEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImageOptimizerEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImageOptimizerEnable');
        if (!$pluginInfo) {
            return parent::isImageOptimizerEnable();
        } else {
            return $this->___callPlugins('isImageOptimizerEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWebPEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWebPEnable');
        if (!$pluginInfo) {
            return parent::isWebPEnable();
        } else {
            return $this->___callPlugins('isWebPEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWebPSupport()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWebPSupport');
        if (!$pluginInfo) {
            return parent::isWebPSupport();
        } else {
            return $this->___callPlugins('isWebPSupport', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isWebPAddPictureTag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWebPAddPictureTag');
        if (!$pluginInfo) {
            return parent::isWebPAddPictureTag();
        } else {
            return $this->___callPlugins('isWebPAddPictureTag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isReplaceWebPInJs()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isReplaceWebPInJs');
        if (!$pluginInfo) {
            return parent::isReplaceWebPInJs();
        } else {
            return $this->___callPlugins('isReplaceWebPInJs', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHTTP2ServerPushEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isHTTP2ServerPushEnabled');
        if (!$pluginInfo) {
            return parent::isHTTP2ServerPushEnabled();
        } else {
            return $this->___callPlugins('isHTTP2ServerPushEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHTTP2ServerPushForCssEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isHTTP2ServerPushForCssEnabled');
        if (!$pluginInfo) {
            return parent::isHTTP2ServerPushForCssEnabled();
        } else {
            return $this->___callPlugins('isHTTP2ServerPushForCssEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHTTP2ServerPushForJsEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isHTTP2ServerPushForJsEnabled');
        if (!$pluginInfo) {
            return parent::isHTTP2ServerPushForJsEnabled();
        } else {
            return $this->___callPlugins('isHTTP2ServerPushForJsEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHTTP2ServerPushForImgEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isHTTP2ServerPushForImgEnabled');
        if (!$pluginInfo) {
            return parent::isHTTP2ServerPushForImgEnabled();
        } else {
            return $this->___callPlugins('isHTTP2ServerPushForImgEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHTTP2ServerPushForFontEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isHTTP2ServerPushForFontEnabled');
        if (!$pluginInfo) {
            return parent::isHTTP2ServerPushForFontEnabled();
        } else {
            return $this->___callPlugins('isHTTP2ServerPushForFontEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImageResponsiveEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImageResponsiveEnable');
        if (!$pluginInfo) {
            return parent::isImageResponsiveEnable();
        } else {
            return $this->___callPlugins('isImageResponsiveEnable', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultImageResponsiveSizes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefaultImageResponsiveSizes');
        if (!$pluginInfo) {
            return parent::getDefaultImageResponsiveSizes();
        } else {
            return $this->___callPlugins('getDefaultImageResponsiveSizes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResizeCommandTargetDirs()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResizeCommandTargetDirs');
        if (!$pluginInfo) {
            return parent::getResizeCommandTargetDirs();
        } else {
            return $this->___callPlugins('getResizeCommandTargetDirs', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAdvancedJsBundling()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAdvancedJsBundling');
        if (!$pluginInfo) {
            return parent::isAdvancedJsBundling();
        } else {
            return $this->___callPlugins('isAdvancedJsBundling', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRjsJsonConfig()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRjsJsonConfig');
        if (!$pluginInfo) {
            return parent::getRjsJsonConfig();
        } else {
            return $this->___callPlugins('getRjsJsonConfig', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isModuleOutputEnabled');
        if (!$pluginInfo) {
            return parent::isModuleOutputEnabled($moduleName);
        } else {
            return $this->___callPlugins('isModuleOutputEnabled', func_get_args(), $pluginInfo);
        }
    }
}
