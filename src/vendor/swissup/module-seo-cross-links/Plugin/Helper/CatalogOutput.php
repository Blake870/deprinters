<?php

namespace Swissup\SeoCrossLinks\Plugin\Helper;

use Swissup\SeoCrossLinks\Helper\Data;
use Swissup\SeoCrossLinks\Model\Filter;
use Swissup\SeoCrossLinks\Model\Link;

class CatalogOutput
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Data $helper
     * @param Filter $filter
     */
    public function __construct(
        Data $helper,
        Filter $filter,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->storeManager = $storeManager;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function aroundCategoryAttribute(
        \Magento\Catalog\Helper\Output $subject,
        $proceed,
        $category,
        $attributeHtml,
        $attributeName
    ) {
        $result = $proceed($category, $attributeHtml, $attributeName);
        $supportedAttributes = [
            'description'
        ];

        if (!$this->helper->IsEnabled() || !in_array($attributeName, $supportedAttributes)) {
            return $result;
        }

        if (!empty($result) && is_string($result)) {
            $result = $this->filter
                ->setMode(Link::SEARCH_IN_СATEGORY)
                ->setStoreId($this->storeManager->getStore()->getId())
                ->filter($result);
        }

        return $result;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function aroundProductAttribute(
        \Magento\Catalog\Helper\Output $subject,
        $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $result = $proceed($product, $attributeHtml, $attributeName);
        $supportedAttributes = [
            'description',
            'short_description',
        ];

        if (!$this->helper->IsEnabled() || !in_array($attributeName, $supportedAttributes)) {
            return $result;
        }

        if (!empty($result) && is_string($result)) {
            $result = $this->filter
                ->setMode(Link::SEARCH_IN_PRODUCT)
                ->setStoreId($this->storeManager->getStore()->getId())
                ->filter($result);
        }

        return $result;
    }
}
