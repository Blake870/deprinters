<?php
namespace Magento\Framework\View\Page\Title;

/**
 * Interceptor class for @see \Magento\Framework\View\Page\Title
 */
class Interceptor extends \Magento\Framework\View\Page\Title implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function set($title)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'set');
        if (!$pluginInfo) {
            return parent::set($title);
        } else {
            return $this->___callPlugins('set', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'get');
        if (!$pluginInfo) {
            return parent::get();
        } else {
            return $this->___callPlugins('get', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getShort()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShort');
        if (!$pluginInfo) {
            return parent::getShort();
        } else {
            return $this->___callPlugins('getShort', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getShortHeading()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShortHeading');
        if (!$pluginInfo) {
            return parent::getShortHeading();
        } else {
            return $this->___callPlugins('getShortHeading', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefault');
        if (!$pluginInfo) {
            return parent::getDefault();
        } else {
            return $this->___callPlugins('getDefault', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function append($suffix)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'append');
        if (!$pluginInfo) {
            return parent::append($suffix);
        } else {
            return $this->___callPlugins('append', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($prefix)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepend');
        if (!$pluginInfo) {
            return parent::prepend($prefix);
        } else {
            return $this->___callPlugins('prepend', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetValue()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetValue');
        if (!$pluginInfo) {
            return parent::unsetValue();
        } else {
            return $this->___callPlugins('unsetValue', func_get_args(), $pluginInfo);
        }
    }
}
