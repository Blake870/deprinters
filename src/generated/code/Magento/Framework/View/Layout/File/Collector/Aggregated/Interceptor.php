<?php
namespace Magento\Framework\View\Layout\File\Collector\Aggregated;

/**
 * Interceptor class for @see \Magento\Framework\View\Layout\File\Collector\Aggregated
 */
class Interceptor extends \Magento\Framework\View\Layout\File\Collector\Aggregated implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\File\FileList\Factory $fileListFactory, \Magento\Framework\View\File\CollectorInterface $baseFiles, \Magento\Framework\View\File\CollectorInterface $themeFiles, \Magento\Framework\View\File\CollectorInterface $overrideBaseFiles, \Magento\Framework\View\File\CollectorInterface $overrideThemeFiles)
    {
        $this->___init();
        parent::__construct($fileListFactory, $baseFiles, $themeFiles, $overrideBaseFiles, $overrideThemeFiles);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(\Magento\Framework\View\Design\ThemeInterface $theme, $filePath)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFiles');
        if (!$pluginInfo) {
            return parent::getFiles($theme, $filePath);
        } else {
            return $this->___callPlugins('getFiles', func_get_args(), $pluginInfo);
        }
    }
}
