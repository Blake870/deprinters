<?php
namespace Magento\Framework\View\FileSystem;

/**
 * Interceptor class for @see \Magento\Framework\View\FileSystem
 */
class Interceptor extends \Magento\Framework\View\FileSystem implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Design\FileResolution\Fallback\File $fallbackFile, \Magento\Framework\View\Design\FileResolution\Fallback\TemplateFile $fallbackTemplateFile, \Magento\Framework\View\Design\FileResolution\Fallback\LocaleFile $fallbackLocaleFile, \Magento\Framework\View\Design\FileResolution\Fallback\StaticFile $fallbackStaticFile, \Magento\Framework\View\Design\FileResolution\Fallback\EmailTemplateFile $fallbackEmailTemplateFile, \Magento\Framework\View\Asset\Repository $assetRepo)
    {
        $this->___init();
        parent::__construct($fallbackFile, $fallbackTemplateFile, $fallbackLocaleFile, $fallbackStaticFile, $fallbackEmailTemplateFile, $assetRepo);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename($fileId, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFilename');
        if (!$pluginInfo) {
            return parent::getFilename($fileId, $params);
        } else {
            return $this->___callPlugins('getFilename', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleFileName($file, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLocaleFileName');
        if (!$pluginInfo) {
            return parent::getLocaleFileName($file, $params);
        } else {
            return $this->___callPlugins('getLocaleFileName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateFileName($fileId, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTemplateFileName');
        if (!$pluginInfo) {
            return parent::getTemplateFileName($fileId, $params);
        } else {
            return $this->___callPlugins('getTemplateFileName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticFileName($fileId, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStaticFileName');
        if (!$pluginInfo) {
            return parent::getStaticFileName($fileId, $params);
        } else {
            return $this->___callPlugins('getStaticFileName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailTemplateFileName($fileId, array $params, $module)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEmailTemplateFileName');
        if (!$pluginInfo) {
            return parent::getEmailTemplateFileName($fileId, $params, $module);
        } else {
            return $this->___callPlugins('getEmailTemplateFileName', func_get_args(), $pluginInfo);
        }
    }
}
