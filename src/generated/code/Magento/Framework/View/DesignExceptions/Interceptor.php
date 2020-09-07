<?php
namespace Magento\Framework\View\DesignExceptions;

/**
 * Interceptor class for @see \Magento\Framework\View\DesignExceptions
 */
class Interceptor extends \Magento\Framework\View\DesignExceptions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, $exceptionConfigPath, $scopeType, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($scopeConfig, $exceptionConfigPath, $scopeType, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeByRequest(\Magento\Framework\App\Request\Http $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getThemeByRequest');
        if (!$pluginInfo) {
            return parent::getThemeByRequest($request);
        } else {
            return $this->___callPlugins('getThemeByRequest', func_get_args(), $pluginInfo);
        }
    }
}
