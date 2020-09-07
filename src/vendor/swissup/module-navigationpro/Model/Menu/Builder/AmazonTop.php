<?php

namespace Swissup\Navigationpro\Model\Menu\Builder;

class AmazonTop extends \Swissup\Navigationpro\Model\Menu\Builder
{
    protected function prepareSettings()
    {
        $this->setSettings([
            'max_depth' => 4,
            'identifier' => 'amazon_top',
            'css_class' => 'navpro-amazon navpro-nowrap',
            'dropdown_settings' => [
                'default' => [
                    'width' => 'small',
                    'layout' => [
                        [
                            'type' => 'children',
                            'columns_count' => 1,
                            'levels_per_dropdown' => 1,
                        ],
                    ],
                ],
                'level1' => [
                    'width' => 'small',
                    'position' => 'center',
                    'layout' => [
                        [
                            'type' => 'children',
                            'columns_count' => 1,
                            'levels_per_dropdown' => 1,
                        ]
                    ],
                ],
                'level2' => [
                    'width' => 'large',
                    'layout' => [
                        [
                            'type' => 'children',
                            'size' => 7,
                            'columns_count' => 2,
                            'direction' => 'vertical',
                            'levels_per_dropdown' => 2,
                        ],
                        [
                            'type' => 'html',
                            'size' => 5,
                            'display_mode' => 'if_has_children',
                            'content' => <<<TEXT
<div style="overflow: hidden; max-height: 550px;">
    {{depend remote_entity.thumbnail}}
        <img class="xs-hide sm-hide" src="{{media url=''}}/catalog/category/{{var remote_entity.thumbnail}}" />
    {{/depend}}
</div>
TEXT
,
                        ],
                    ],
                ],
            ],
        ]);
    }

    protected function prepareItems()
    {
        $this->setItems([
            'departments' => [
                'name' => 'Departments',
                'css_class' => 'navpro-departments',
                'url_path' => 'departments',
                'items' => [
                    'categories' => [
                        'method' => 'importCategories'
                    ],
                ],
            ],
            'sale' => [
                'name' => 'Sale',
                'url_path' => '#',
                'css_class' => 'ml3 xs-ml0 navpro-sale',
            ],
            'brands' => [
                'name' => 'Brands',
                'url_path' => '#',
                'css_class' => 'navpro-brands',
            ],
            'trends' => [
                'name' => 'Trends',
                'url_path' => '#',
                'css_class' => 'navpro-trends',
            ],
            'faq' => [
                'name' => 'FAQ',
                'url_path' => 'faq',
                'css_class' => 'ml-auto xs-ml0 navpro-faq',
            ],
            'services' => [
                'name' => 'Services',
                'url_path' => 'services',
                'css_class' => 'navpro-services',
            ],
            'wishlist' => [
                'name' => 'Wishlist',
                'url_path' => '#',
                'is_active' => '0',
                'css_class' => 'xs-hide navpro-wishlist',
                'dropdown_settings' => [
                    'width' => 'small',
                    'layout' => [
                        [
                            'type' => 'html',
                            'content' => '{{block class="Magento\Wishlist\Block\Customer\Sidebar" template="Magento_Wishlist::sidebar.phtml" name="navigation.wishlist"}}',
                        ],
                    ],
                ],
            ],
            'compare' => [
                'name' => 'Compare',
                'url_path' => '#',
                'css_class' => 'navpro-compare',
                'dropdown_settings' => [
                    'width' => 'small',
                    'layout' => [
                        [
                            'type' => 'html',
                            'content' => '{{block class="Swissup\Navigationpro\Block\Common\Catalog\Compare\Sidebar" template="Magento_Catalog::product/compare/sidebar.phtml" name="navigation.compare"}}',
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
