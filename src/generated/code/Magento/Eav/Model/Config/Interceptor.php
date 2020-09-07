<?php
namespace Magento\Eav\Model\Config;

/**
 * Interceptor class for @see \Magento\Eav\Model\Config
 */
class Interceptor extends \Magento\Eav\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\CacheInterface $cache, \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory, \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\Validator\UniversalFactory $universalFactory, ?\Magento\Framework\Serialize\SerializerInterface $serializer = null, ?\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig = null, $attributesForPreload = [])
    {
        $this->___init();
        parent::__construct($cache, $entityTypeFactory, $entityTypeCollectionFactory, $cacheState, $universalFactory, $serializer, $scopeConfig, $attributesForPreload);
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCache');
        if (!$pluginInfo) {
            return parent::getCache();
        } else {
            return $this->___callPlugins('getCache', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clear');
        if (!$pluginInfo) {
            return parent::clear();
        } else {
            return $this->___callPlugins('clear', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isCacheEnabled');
        if (!$pluginInfo) {
            return parent::isCacheEnabled();
        } else {
            return $this->___callPlugins('isCacheEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType($code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityType');
        if (!$pluginInfo) {
            return parent::getEntityType($code);
        } else {
            return $this->___callPlugins('getEntityType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entityType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributes');
        if (!$pluginInfo) {
            return parent::getAttributes($entityType);
        } else {
            return $this->___callPlugins('getAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($entityType, $code)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttribute');
        if (!$pluginInfo) {
            return parent::getAttribute($entityType, $code);
        } else {
            return $this->___callPlugins('getAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributeCodes($entityType, $object = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityAttributeCodes');
        if (!$pluginInfo) {
            return parent::getEntityAttributeCodes($entityType, $object);
        } else {
            return $this->___callPlugins('getEntityAttributeCodes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributes($entityType, $object = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityAttributes');
        if (!$pluginInfo) {
            return parent::getEntityAttributes($entityType, $object);
        } else {
            return $this->___callPlugins('getEntityAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function importAttributesData($entityType, array $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'importAttributesData');
        if (!$pluginInfo) {
            return parent::importAttributesData($entityType, $attributes);
        } else {
            return $this->___callPlugins('importAttributesData', func_get_args(), $pluginInfo);
        }
    }
}
