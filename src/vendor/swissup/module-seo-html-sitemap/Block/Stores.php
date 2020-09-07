<?php
namespace Swissup\SeoHtmlSitemap\Block;

use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\Element\Template;
use \Swissup\SeoHtmlSitemap\Helper\Config;
use \Swissup\SeoHtmlSitemap\Model\Link as LinkModel;

class Stores extends Template implements \Magento\Framework\DataObject\IdentityInterface
{
    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    public function getCollection()
    {
        if (!$this->config->showStores()) {
            return false;
        }

        $collection = $this->_storeManager->getStores();

        return $collection;
    }

    public function getItemUrl($store)
    {
        return $store->getUrl();
    }

    public function getItemName($store)
    {
        return $store->getName();
    }

    public function getIdentities()
    {
        return [LinkModel::CACHE_TAG . '_' . 'stores'];
    }
}
