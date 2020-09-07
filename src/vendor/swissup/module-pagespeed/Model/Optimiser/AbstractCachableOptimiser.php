<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;

abstract class AbstractCachableOptimiser extends AbstractOptimiser
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = \Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER;

    /**
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * Cache state
     *
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    private $cacheState;

    /**
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     *
     * @var array
     */
    private $cacheLayers = [];

    /**
     * @param Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        $this->serializer = $serializer;

        parent::__construct($config);
    }


    /**
     * @param string $src
     * @return array|false
     */
    protected function loadCache($src)
    {
        $isCacheable = $this->cacheState->isEnabled(self::CACHE_GROUP);
        if (!$isCacheable) {
            return false;
        }
        $cacheKey = sha1($src);
        $cacheData = $this->cache->load($cacheKey);

        if ($cacheData) {
            $cacheData = $this->serializer->unserialize($cacheData);
        }

        return $cacheData;
    }

    /**
     *
     * @param string $src
     * @param array $data
     * @return $this
     */
    protected function saveCache($src, $data)
    {
        if (!$this->cacheState->isEnabled(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = sha1($src);
        $cacheTags = [self::CACHE_GROUP];

        $data = $this->serializer->serialize($data);
        $this->cache->save($data, $cacheKey, $cacheTags);
        return $this;
    }

    /**
     *
     * @param string $cacheLayerId
     */
    protected function _loadCacheLayer($cacheLayerId)
    {
        if (!isset($this->cacheLayers[$cacheLayerId])) {
            $this->cacheLayers[$cacheLayerId] = $this->loadCache($cacheLayerId);
        }

        if (!isset($this->cacheLayers[$cacheLayerId])) {
            $this->cacheLayers[$cacheLayerId] = [];
        }
    }

    /**
     *
     * @param  string $cacheLayerId
     * @param  string $id
     * @return mixed
     */
    protected function loadCacheLayerValue($cacheLayerId, $id)
    {
        $this->_loadCacheLayer($cacheLayerId);
        return isset($this->cacheLayers[$cacheLayerId][$id]) ? $this->cacheLayers[$cacheLayerId][$id] : false;
    }

    /**
     *
     * @param string $cacheLayerId
     * @param string $id
     * @param mixed $value
     */
    protected function setCacheLayerValue($cacheLayerId, $id, $value)
    {
        $this->_loadCacheLayer($cacheLayerId);
        $this->cacheLayers[$cacheLayerId][$id] = $value;

        return $this;
    }

    /**
     *
     * @param string $cacheLayerId
     * @return $this
     */
    protected function saveCacheLayer($cacheLayerId)
    {
        $value = isset($this->cacheLayers[$cacheLayerId]) ? $this->cacheLayers[$cacheLayerId] : [];
        if (!empty($value)) {
            $this->saveCache($cacheLayerId, $value);
        }
        return $this;
    }
}
