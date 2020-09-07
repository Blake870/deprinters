<?php
namespace Magento\Framework\App\View\Asset\Publisher;

/**
 * Interceptor class for @see \Magento\Framework\App\View\Asset\Publisher
 */
class Interceptor extends \Magento\Framework\App\View\Asset\Publisher implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Filesystem $filesystem, \Magento\Framework\App\View\Asset\MaterializationStrategy\Factory $materializationStrategyFactory, \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory)
    {
        $this->___init();
        parent::__construct($filesystem, $materializationStrategyFactory, $writeFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publish');
        if (!$pluginInfo) {
            return parent::publish($asset);
        } else {
            return $this->___callPlugins('publish', func_get_args(), $pluginInfo);
        }
    }
}
