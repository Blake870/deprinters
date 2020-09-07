<?php
namespace Swissup\Pagespeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mobile_Detect as MobileDetect;

class Config extends AbstractHelper
{
    const CONFIG_XML_PATH_ENABLE             = 'pagespeed/main/enable';
    const CONFIG_XML_PATH_DEV_MODE           = 'pagespeed/main/devmode';
    const CONFIG_XML_PATH_HTTP2_PUSH         = 'pagespeed/main/server_push';
    const CONFIG_XML_PATH_CONTENT_ENABLE     = 'pagespeed/content/enable';
    const CONFIG_XML_PATH_CONTENT_JS         = 'pagespeed/content/js';
    const CONFIG_XML_PATH_CONTENT_CSS        = 'pagespeed/content/css';
    const CONFIG_XML_PATH_JS_MERGE           = 'dev/js/merge_files';
    const CONFIG_XML_PATH_JS_ENABLE_JS_BUNDLING = 'dev/js/enable_js_bundling';
    const CONFIG_XML_PATH_JS_ENABLE_ADVANCED_JS_BUNDLING = 'pagespeed/js/enable_advanced_js_bundling';
    const CONFIG_XML_PATH_JS_RJS_BUILD_CONFIG = 'pagespeed/js/rjs_build_config';
    const CONFIG_XML_PATH_JS_DEFER           = 'pagespeed/js/defer_enable';
    // const CONFIG_XML_PATH_JS_MOVE_INLINE_TO_BOTTOM = 'dev/js/move_inline_to_bottom';
    const CONFIG_XML_PATH_JS_DEFER_UNPACK    = 'pagespeed/js/defer_unpack';
    const CONFIG_XML_PATH_CSS_DEFER          = 'pagespeed/css/defer_enable';
    const CONFIG_XML_PATH_CSS_DEFER_ONLOAD   = 'pagespeed/css/defer_onload';
    const CONFIG_XML_PATH_USE_CSS_CRITICAL_PATH = 'dev/css/use_css_critical_path';
    const CONFIG_XML_PATH_CSS_CRITICAL_ENABLE = 'pagespeed/css/critical_enable';
    const CONFIG_XML_PATH_CSS_CRITICAL_DEFAULT = 'pagespeed/css/critical_default';
    const CONFIG_XML_PATH_EXPIRE_ENABLE      = 'pagespeed/expire/enable';
    const CONFIG_XML_PATH_EXPIRE_TTL         = 'pagespeed/expire/ttl';
    const CONFIG_XML_PATH_DNSPREFETCH_ENABLE = 'pagespeed/dnsprefetch/enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_ENABLE = 'pagespeed/image/optimize_enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_ENABLE = 'pagespeed/image/optimize_webp_enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_PICTURE_ADD = 'pagespeed/image/optimize_webp_picture_add';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_ENABLE = 'pagespeed/image/lazyload_enable';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_IGNORE = 'pagespeed/image/lazyload_ignore';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_PLACEHOLDER_ENABLE = 'pagespeed/image/lazyload_placeholder_enable';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_PLACEHOLDER = 'pagespeed/image/lazyload_placeholder';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_OFFSET = 'pagespeed/image/lazyload_offset';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_MOBILE_OFFSET = 'pagespeed/image/lazyload_mobile_offset';
    const CONFIG_XML_PATH_IMAGE_DIMENSION  = 'pagespeed/image/dimension';
    const CONFIG_XML_PATH_IMAGE_RESPONSIVE_ENABLE = 'pagespeed/image/responsive';
    const CONFIG_XML_PATH_IMAGE_RESPONSIVE_SIZES = 'pagespeed/image/default_responsive_sizes';

    /**
     * @var string
     */
    private $stateMode;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectoryWriter;

    /**
     *
     * @var \Swissup\Pagespeed\Model\Css\FontDisplay
     */
    private $cssImprover;

    /**
     *
     * @var MobileDetect
     */
    private $detector;

    /**
     *
     * @var array
     */
    private $lazyloadIgnores;

    /**
     * @param Context $context
     * @param AppState $state
     * @param Filesystem $filesystem
     * @param \Swissup\Pagespeed\Model\Css\FontDisplay $improver
     * @param MobileDetect $detector
     */
    public function __construct(
        Context $context,
        AppState $state,
        Filesystem $filesystem,
        \Swissup\Pagespeed\Model\Css\FontDisplay $cssImprover,
        MobileDetect $detector
    ) {
        parent::__construct($context);
        $this->stateMode = $state->getMode();
        $this->mediaDirectoryWriter = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->cssImprover = $cssImprover;
        $this->detector = $detector;
    }

    /**
     *
     * @param  string $key
     * @return mixed
     */
    private function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @param  string $key
     * @return boolean
     */
    private function isSetFlag($key)
    {
        return $this->scopeConfig->isSetFlag($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return boolean
     */
    public function isDeveloperMode()
    {
        return $this->stateMode === AppState::MODE_DEVELOPER;
    }

    /**
     *
     * @return boolean
     */
    public function isDeveloperModeIgnored()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_DEV_MODE);
    }

    /**
     *
     * @return boolean
     */
    public function isEnable()
    {
        return (!$this->isDeveloperMode() || $this->isDeveloperModeIgnored()) && $this->isSetFlag(self::CONFIG_XML_PATH_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isContentMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isContentJsMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_JS);
    }

    /**
     *
     * @return boolean
     */
    public function isContentCssMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_CSS);
    }

    /**
     *
     * @return boolean
     */
    public function isAddExpireEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_EXPIRE_ENABLE);
    }

    /**
     *
     * @return int
     */
    public function getExpireTTL()
    {
        return (int) $this->getConfig(self::CONFIG_XML_PATH_EXPIRE_TTL);
    }

    /**
     *
     * @return boolean
     */
    public function isDnsPrefetchEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_DNSPREFETCH_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isJsMergeEnable()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_JS_MERGE);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferJsEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_JS_DEFER);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferJsUnpackEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_JS_DEFER_UNPACK);
    }

    /**
     *
     * @return boolean
     */
    public function isMergeJsFilesForMobileDisabled()
    {
        return false && $this->detector->isMobile();
        // return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_DEFER_ONLOAD);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferCssEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_DEFER);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferCssOnloadEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_DEFER_ONLOAD);
    }

    /**
     *
     * @return boolean
     */
    public function isMobile()
    {
        return $this->detector->isMobile();
    }

    /**
     *
     * @return boolean
     */
    public function isMergeCssFilesForMobileDisabled()
    {
        return false && $this->isMobile();
        // return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_DEFER_ONLOAD);
    }

    /**
     *
     * @return boolean
     */
    public function isAutoAddFontDisplayForMergedCss()
    {
        return $this->isEnable();
        // return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_);
    }

    /**
     *
     * @return boolean
     */
    public function isImageLazyLoadEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_ENABLE);
    }

    /**
     *
     * @return array
     */
    public function getLazyloadIgnores()
    {
        $ignores = explode("\n", $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_IGNORE));
        foreach ($ignores as &$ignore) {
            $ignore = trim($ignore, " \r");
        }
        $ignores = array_filter($ignores);
        return $ignores;
    }

    /**
     *
     * @param string $src
     * @return boolean
     */
    public function isImageLazyLoadIgnored($src)
    {
        if ($this->lazyloadIgnores === null) {
            $this->lazyloadIgnores = $this->getLazyloadIgnores();
        }
        foreach ($this->lazyloadIgnores as $ignore) {
            if (false !== strstr($src, $ignore)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isImageLazyLoadPlaceholderEnable()
    {
        return $this->isImageLazyLoadEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_PLACEHOLDER_ENABLE);
    }


    /**
     *
     * @return string
     */
    public function getLazyloadPlaceholder()
    {
        $placeholder = $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_PLACEHOLDER);
        $placeholder = trim($placeholder, "\n\r ");
        return $placeholder;
    }

    /**
     *
     * @return int
     */
    public function getLazyloadOffset()
    {
        return  (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_OFFSET);
    }

    /**
     *
     * @return int
     */
    public function getLazyloadMobileOffset()
    {
        return  (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_MOBILE_OFFSET);
    }

    /**
     *
     * @return boolean
     */
    public function isUseCssCriticalPathEnable()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_USE_CSS_CRITICAL_PATH);
    }

    /**
     *
     * @return boolean
     */
    public function isCriticalCssEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_CRITICAL_ENABLE);
    }

    /**
     *
     * @return string
     */
    public function getDefaultCriticalCss()
    {
        $value = $this->getConfig(self::CONFIG_XML_PATH_CSS_CRITICAL_DEFAULT);

        $filename = $value;
        $writer = $this->mediaDirectoryWriter;

        if ($writer->isExist($filename) &&
            $writer->isFile($filename) &&
            $writer->isReadable($filename)
        ) {
            $_value = $writer->readFile($filename);
            if (!empty($_value)) {
                $value = $_value;

                // add font-display:swap
                // https://css-tricks.com/font-display-masses/
                if ($this->isAutoAddFontDisplayForMergedCss()) {
                    $value = $this->cssImprover->process($value);
                }
                $value = str_replace(['http://', 'https://'], '//', $value);
            }
        }

        return trim($value);
    }

    /**
     *
     * @return boolean
     */
    public function isDimensionEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_DIMENSION);
    }

    /**
     *
     * @return boolean
     */
    public function isImageOptimizerEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isWebPEnable()
    {
        return $this->isImageOptimizerEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isWebPSupport()
    {
        $detector = $this->detector;

        return $this->isWebPEnable()
            && ($detector->is('Chrome')
                || $detector->is('Opera')
                || $detector->isAndroidOS()
                || $detector->version('Firefox') >= 65
                || $detector->version('Chrome') > 32
            );
    }

    /**
     *
     * @return boolean
     */
    public function isWebPAddPictureTag()
    {
        return $this->isWebPEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_PICTURE_ADD);
    }

    /**
     *
     * @return boolean
     */
    public function isReplaceWebPInJs()
    {
        return true && $this->isWebPEnable();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushEnabled()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_HTTP2_PUSH);
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForCssEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForJsEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForImgEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForFontEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isImageResponsiveEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_RESPONSIVE_ENABLE);
    }

    /**
     *
     * @return string
     */
    public function getDefaultImageResponsiveSizes()
    {
        return $this->getConfig(self::CONFIG_XML_PATH_IMAGE_RESPONSIVE_SIZES);
    }

    /**
     *
     * @return array of string
     */
    public function getResizeCommandTargetDirs()
    {
        return [
            'wysiwyg',
            'catalog/category',
            'easybanner',
            'easyslide',
            'swissup',
            'highlight',
            'easycatalogimg',
            'prolabels',
            'testimonials',
            'mageplaza'
        ];
    }

    /**
     *
     * @return boolean
     */
    public function isAdvancedJsBundling()
    {
        return $this->isEnable()
            && !$this->isSetFlag(self::CONFIG_XML_PATH_JS_ENABLE_JS_BUNDLING)
            && $this->isSetFlag(self::CONFIG_XML_PATH_JS_ENABLE_ADVANCED_JS_BUNDLING);
    }

    /**
     *
     * @return string
     */
    public function getRjsJsonConfig()
    {
        return $this->getConfig(self::CONFIG_XML_PATH_JS_RJS_BUILD_CONFIG);
    }
}
