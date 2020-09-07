<?php
/**
 * Plugins for methods in \Magento\Sitemap\Model\ResourceModel\Catalog\Product
 */
namespace Swissup\Hreflang\Plugin\Sitemap\ResourceModel\Catalog;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Product extends AbstractEntity
{
    /**
     * {@inheritdoc}
     */
    protected $entityType = 'product';
}
