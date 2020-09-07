<?php

namespace Swissup\SeoUrls\Model;

class Attribute
{
    /**
     * @param \Swissup\SeoUrls\Helper\Data                          $helper
     * @param \Swissup\SeoUrls\Model\ResourceModel\Attribute\Action $attributeAction
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Data $helper,
        \Swissup\SeoUrls\Model\ResourceModel\Attribute\Action $attributeAction
    ) {
        $this->helper = $helper;
        $this->attributeAction = $attributeAction;
    }

    /**
     * Get in-URL label for attribute
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @return string|null
     */
    public function getInUrlLabel(\Magento\Framework\DataObject $attribute)
    {
        $storeId = $this->helper->getCurrentStore()->getId();
        $labels = $this->attributeAction->getInUrlLabels($attribute);
        return isset($labels[$storeId])
            ? $labels[$storeId]['value']
            : (
                isset($labels[0])
                    ? $labels[0]['value']
                    : null
                );
    }

    /**
     * Get original store label of attribute converted into seo-friendly string
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @return string
     */
    public function getFallbackLabel(\Magento\Framework\DataObject $attribute)
    {
        $storeId = $this->helper->getCurrentStore()->getId();
        $labels = $attribute->getStoreLabels();
        $label = isset($labels[$storeId])
            ? $labels[$storeId]
            : $attribute->getFrontendLabel();
        return $this->helper->getSeoFriendlyString($label);
    }

    /**
     * Get in-URL label for attribuet with fallback to converted orignal label
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @return string
     */
    public function getStoreLabel(\Magento\Framework\DataObject $attribute)
    {
        $label = $this->getInUrlLabel($attribute);
        if (!$label) {
            $label = $this->getFallbackLabel($attribute);
        }

        return $label;
    }

    /**
     * Get in-URL value for attribute
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @param  \Magento\Framework\DataObject $option
     * @return string|null
     */
    public function getInUrlValue(
        \Magento\Framework\DataObject $attribute,
        \Magento\Framework\DataObject $option
    ) {
        $storeId = $this->helper->getCurrentStore()->getId();
        $values = $this->attributeAction->getInUrlValues(
            $attribute->getId(),
            $option->getValue()
        );
        return isset($values[$storeId])
            ? $values[$storeId]['url_value']
            : (
                isset($values[0])
                    ? $values[0]['url_value']
                    : null
                );
    }

    /**
     * Get original value of attribute converted into seo-friendly string
     *
     * @param  \Magento\Framework\DataObject $option
     * @return string
     */
    public function getFallbackValue(\Magento\Framework\DataObject $option)
    {
        return $this->helper->getSeoFriendlyString($option->getLabel());
    }

    /**
     * Get in-URL value for attribuet with fallback to converted orignal value
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @param  \Magento\Framework\DataObject $option
     * @return
     */
    public function getStoreValue(
        \Magento\Framework\DataObject $attribute,
        \Magento\Framework\DataObject $option
    ) {
        $value = $this->getInUrlValue($attribute, $option);
        if (!$value) {
            $value = $this->getFallbackValue($option);
        }

        return $value;
    }

    /**
     * Check is nofollow allowed for attribute
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @return boolean
     */
    public function isNofollow(\Magento\Framework\DataObject $attribute)
    {
        $advanced = $this->attributeAction->getAdvancedProps($attribute);
        return isset($advanced['is_nofollow'])
            ? (bool)$advanced['is_nofollow']
            : false;
    }
}
