<?php
namespace Magento\Framework\View\Url\CssResolver;

/**
 * Interceptor class for @see \Magento\Framework\View\Url\CssResolver
 */
class Interceptor extends \Magento\Framework\View\Url\CssResolver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function relocateRelativeUrls($cssContent, $relatedPath, $filePath)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'relocateRelativeUrls');
        if (!$pluginInfo) {
            return parent::relocateRelativeUrls($cssContent, $relatedPath, $filePath);
        } else {
            return $this->___callPlugins('relocateRelativeUrls', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceRelativeUrls($cssContent, $inlineCallback)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'replaceRelativeUrls');
        if (!$pluginInfo) {
            return parent::replaceRelativeUrls($cssContent, $inlineCallback);
        } else {
            return $this->___callPlugins('replaceRelativeUrls', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function aggregateImportDirectives($cssContent)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'aggregateImportDirectives');
        if (!$pluginInfo) {
            return parent::aggregateImportDirectives($cssContent);
        } else {
            return $this->___callPlugins('aggregateImportDirectives', func_get_args(), $pluginInfo);
        }
    }
}
