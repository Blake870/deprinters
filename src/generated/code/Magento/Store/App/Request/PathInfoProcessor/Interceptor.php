<?php
namespace Magento\Store\App\Request\PathInfoProcessor;

/**
 * Interceptor class for @see \Magento\Store\App\Request\PathInfoProcessor
 */
class Interceptor extends \Magento\Store\App\Request\PathInfoProcessor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\App\Request\StorePathInfoValidator $storePathInfoValidator, \Magento\Framework\App\Config\ReinitableConfigInterface $config)
    {
        $this->___init();
        parent::__construct($storePathInfoValidator, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Framework\App\RequestInterface $request, $pathInfo) : string
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'process');
        if (!$pluginInfo) {
            return parent::process($request, $pathInfo);
        } else {
            return $this->___callPlugins('process', func_get_args(), $pluginInfo);
        }
    }
}
