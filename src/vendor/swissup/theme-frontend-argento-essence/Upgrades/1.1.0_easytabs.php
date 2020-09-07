<?php

namespace Swissup\ThemeFrontendArgentoEssence\Upgrades;

class Easytabs extends \Swissup\Core\Model\Module\Upgrade
{
    public function getCommands()
    {
        return [
            'Easytabs' => $this->getEasytabs()
        ];
    }

    public function getEasytabs()
    {
        return [
            [
                'title' => 'Upsells',
                'alias' => 'upsells',
                'block' => 'Magento\Catalog\Block\Product\ProductList\Upsell',
                'block_arguments' => 'type:upsell',
                'sort_order' => 40,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/list/items.phtml',
                'widget_unset' => 'product.info.upsell'
            ],
            [
                'title' => 'Related Products',
                'alias' => 'related',
                'block' => 'Magento\Catalog\Block\Product\ProductList\Related',
                'block_arguments' => 'type:related',
                'sort_order' => 50,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/list/items.phtml',
                'widget_unset' => 'catalog.product.related'
            ],
            [
                'title' => 'Questions ({{eval code="getCount()"}})',
                'alias' => 'questions',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 60,
                'status' => 1,
                'widget_block' => 'Swissup\Askit\Block\Question\Widget',
                'widget_template' => 'template.phtml',
                'widget_unset' => 'askit_listing,askit_form'
            ]
        ];
    }
}
