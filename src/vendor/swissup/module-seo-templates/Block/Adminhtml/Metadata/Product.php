<?php

namespace Swissup\SeoTemplates\Block\Adminhtml\Metadata;

class Product extends AbstractBlock
{
    /**
     * {@inheritdoc}
     */
    public function getCurrentEntityType()
    {
        return \Swissup\SeoTemplates\Model\Template::ENTITY_TYPE_PRODUCT;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentEntity()
    {
        return $this->registry->registry('current_product');
    }
}
