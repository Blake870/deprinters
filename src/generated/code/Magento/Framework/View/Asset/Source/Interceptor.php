<?php
namespace Magento\Framework\View\Asset\Source;

/**
 * Interceptor class for @see \Magento\Framework\View\Asset\Source
 */
class Interceptor extends \Magento\Framework\View\Asset\Source implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Filesystem $filesystem, \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory, \Magento\Framework\View\Asset\PreProcessor\Pool $preProcessorPool, \Magento\Framework\View\Design\FileResolution\Fallback\StaticFile $fallback, \Magento\Framework\View\Design\Theme\ListInterface $themeList, \Magento\Framework\View\Asset\PreProcessor\ChainFactoryInterface $chainFactory)
    {
        $this->___init();
        parent::__construct($filesystem, $readFactory, $preProcessorPool, $fallback, $themeList, $chainFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFile');
        if (!$pluginInfo) {
            return parent::getFile($asset);
        } else {
            return $this->___callPlugins('getFile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getContent');
        if (!$pluginInfo) {
            return parent::getContent($asset);
        } else {
            return $this->___callPlugins('getContent', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContentType(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSourceContentType');
        if (!$pluginInfo) {
            return parent::getSourceContentType($asset);
        } else {
            return $this->___callPlugins('getSourceContentType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findSource(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'findSource');
        if (!$pluginInfo) {
            return parent::findSource($asset);
        } else {
            return $this->___callPlugins('findSource', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getContentType');
        if (!$pluginInfo) {
            return parent::getContentType($path);
        } else {
            return $this->___callPlugins('getContentType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findRelativeSourceFilePath(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'findRelativeSourceFilePath');
        if (!$pluginInfo) {
            return parent::findRelativeSourceFilePath($asset);
        } else {
            return $this->___callPlugins('findRelativeSourceFilePath', func_get_args(), $pluginInfo);
        }
    }
}
