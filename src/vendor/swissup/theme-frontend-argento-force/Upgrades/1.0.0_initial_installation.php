<?php
namespace Swissup\ThemeFrontendArgentoForce\Upgrades;

use Swissup\Easytabs\Model\Entity as TabsModel;

class InitialInstallation extends \Swissup\Core\Model\Module\Upgrade
{
    public function up()
    {
        // Unset existing tabs.
        $this->unsetEasytab(
            'Swissup\Easytabs\Block\Tab\Template',
            $this->getStoreIds(),
            'questions'
        );
        $this->unsetEasytab(
            'Magento\Catalog\Block\Product\View\Attributes',
            $this->getStoreIds()
        );

        // Create new tabs
        foreach ($this->getEasytabs() as $data) {
            $tab = $this->objectManager->create(TabsModel::class);
            $tab->setData($data)
                ->setStores($this->getStoreIds());
            try {
                $this->unsetEasytab($tab->getBlock(), $this->getStoreIds(), $tab->getAlias());
                $tab->save();
            } catch (\Exception $e) {
                $this->getMessageLogger()->addError('easytabs_tab_save', array(
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ));
                continue;
            }
        }
    }

    public function getCommands()
    {
        return [
            'Configuration' => $this->getConfiguration(),
            'ConfigurationReplacement' => $this->getConfigurationReplacement(),
            'CmsBlock'      => $this->getCmsBlocks(),
            'CmsPage'       => $this->getCmsPages(),
            'Easyslide'     => $this->getEasyslide(),
            // 'Easytabs'      => $this->getEasytabs(), // create tabs in up() method
            'Easybanner'    => $this->getEasybanner(),
            'ProductAttribute' => $this->getProductAttribute(),
            'Products' => [],
            'Navigationpro' => $this->getNavigationpro(),
        ];
    }

    private function renderContent($fileId)
    {
        $block = $this->objectManager->get(\Magento\Framework\View\Element\Template::class);
        $templateEngine = $this->objectManager->get(\Magento\Framework\View\TemplateEngine\Php::class);
        $fileName = dirname(__FILE__) . "/1.0.0_content/{$fileId}";
        return file_exists($fileName) ? $templateEngine->render($block, $fileName) : '';
    }

    public function getConfigurationReplacement()
    {
        return [
            'design/head/includes' => [
                '<link  rel="stylesheet" type="text/css"  media="all" href="{{MEDIA_URL}}styles.css" />' => ''
            ]
        ];
    }

    public function getConfiguration()
    {
        $storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeIds = $this->getStoreIds();
        $storeToInstall = $storeManager->getStore(reset($storeIds));

        return [
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-force'),
            'cms/wysiwyg/enabled' => 'hidden',
            'catalog/frontend/grid_per_page_values' => '12,20,32',
            'catalog/frontend/grid_per_page' => '20',
            'catalog/frontend/list_per_page_values' => '5,10,15,20,25',
            'catalog/frontend/list_per_page' => '5',

            'ajaxsearch/main/enable' => 1,
            'ajaxsearch/main/limit' => 60,
            'ajaxsearch/folded/enable' => 1,
            'ajaxsearch/design/layout' => 'grid',
            'ajaxsearch/product/limit' => 21,
            'ajaxsearch/category/filter' => 0,
            'ajaxsearch/autocomplete/show_on_focus' => 0,

            'fblike/product/enabled' => 1,
            'fblike/product/layout' => 'custom',
            'fblike/category/enabled' => 0,

            'hovergallery/general/enabled' => 1,

            'lightboxpro/general/thumbnails' => 'vertical',
            'lightboxpro/size/thumbnail_height' => '100',
            'lightboxpro/size/thumbnail_width' => '80',
            'lightboxpro/size/thumbnail_margin' => '10',
            'lightboxpro/size/thumbnail_border_width' => '3',

            'quantityswitcher/general/enabled' => 1,
            'quantityswitcher/general/switcher_type' => 'dropdown',

            'prolabels/on_sale/product/active' => '1',
            'prolabels/on_sale/product/position' => 'top-left',
            'prolabels/on_sale/product/text' => $this->_getProlabelText('product', '-#discount_percent#%'),
            'prolabels/on_sale/product/custom' => $this->_getProlabelCustom('product', '#e8e571'),
            'prolabels/on_sale/product/round_value' => '1',
            'prolabels/on_sale/category/active' => '1',
            'prolabels/on_sale/category/position' => 'top-left',
            'prolabels/on_sale/category/text' => $this->_getProlabelText('category', '-#discount_percent#%'),
            'prolabels/on_sale/category/custom' => $this->_getProlabelCustom('category', '#e8e571', '16px'),
            'prolabels/on_sale/category/round_value' => '1',

            'prolabels/is_new/product/active' => '1',
            'prolabels/is_new/product/position' => 'bottom-right',
            'prolabels/is_new/product/text' => $this->_getProlabelText('product', 'NEW!'),
            'prolabels/is_new/product/custom' => $this->_getProlabelCustom('product', '#91ecf0'),
            'prolabels/is_new/category/active' => '1',
            'prolabels/is_new/category/position' => 'bottom-right',
            'prolabels/is_new/category/text' => $this->_getProlabelText('category', 'NEW!'),
            'prolabels/is_new/category/custom' => $this->_getProlabelCustom('category', '#91ecf0'),

            'prolabels/out_stock/product/active' => '1',
            'prolabels/out_stock/product/position' => 'bottom-right',
            'prolabels/out_stock/product/text' => $this->_getProlabelText('product', 'OUT OF STOCK'),
            'prolabels/out_stock/product/custom' => $this->_getProlabelCustom('product', '#D6DBE0'),
            'prolabels/out_stock/category/active' => '1',
            'prolabels/out_stock/category/position' => 'bottom-right',
            'prolabels/out_stock/category/text' => $this->_getProlabelText('category', 'OUT OF STOCK'),
            'prolabels/out_stock/category/custom' => $this->_getProlabelCustom('category', '#D6DBE0'),

            'prolabels/in_stock/product/active' => '1',
            'prolabels/in_stock/product/position' => 'bottom-left',
            'prolabels/in_stock/product/text' => $this->_getProlabelText('product', 'ONLY #stock_item# LEFT!'),
            'prolabels/in_stock/product/custom' => $this->_getProlabelCustom('product', '#fe9448'),
            'prolabels/in_stock/category/active' => '1',
            'prolabels/in_stock/category/position' => 'bottom-left',
            'prolabels/in_stock/category/text' => $this->_getProlabelText('category', 'ONLY #stock_item# LEFT!'),
            'prolabels/in_stock/category/custom' => $this->_getProlabelCustom('category', '#fe9448'),

            'prolabels/output/category_content' => '.product-item-details .price-box',

            'soldtogether/order/enabled' => 1,
            'soldtogether/order/random' => 1,
            'soldtogether/customer/enabled' => 1,
            'soldtogether/customer/random' => 1,

            // SEO Suite settings
            'richsnippets/general/enabled' => 1,
            'richsnippets/website/siteurl' => $storeToInstall->getBaseUrl(),
            'swissup_seopager/general/enabled' => 1,
            'swissup_seopager/general/strategy' => 2,
            'swissup_seopager/general/strategy_no_view_all' => 2,
            'swissup_seourls/general/enabled' => 1,
            'swissup_seourls/search/term_place' => 0,
            'swissup_seourls/layered_navigation/separate_filters' => 0,
            'swissup_seourls/cms/redirect_to_home' => 1,

            'testimonials/form/layout' => '1column',

            'pagespeed/image/lazyload_ignore' => 'media/easyslide'
        ];
    }

    public function getCmsBlocks()
    {
        return [
            'header_slider' => [
                'title' => 'Header Slider',
                'identifier' => 'header_slider',
                'is_active' => 1,
                'content' => $this->renderContent('block/header_slider.phtml')
            ],
            'benefits' => [
                'title' => 'Benefits',
                'identifier' => 'benefits',
                'is_active' => 1,
                'content' => $this->renderContent('block/benefits.phtml')
            ],
            'hot-stuff' => [
                'title' => 'Hot Stuff',
                'identifier' => 'hot-stuff',
                'is_active' => 1,
                'content' => $this->renderContent('block/hot-stuff.phtml')
            ],
            'sizing_chart' => [
                'title' => 'Sizing Charts',
                'identifier' => 'sizing_chart',
                'is_active' => 1,
                'content' => $this->renderContent('block/sizing_chart.phtml')
            ],
            'footer_cms_content' => [
                'title' => 'Footer CMS Content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => $this->renderContent('block/footer_cms_content.phtml')
            ],
            'footer_information' => [
                'title' => 'Footer Information',
                'identifier' => 'footer_information',
                'is_active' => 1,
                'content' => $this->renderContent('block/footer_information.phtml')
            ],
            'contact' => [
                'title' => 'Contact Us',
                'identifier' => 'contact_aside_cms',
                'is_active' => 1,
                'content' => $this->renderContent('block/contact.phtml')
            ]
        ];
    }

    public function getCmsPages()
    {
        return [
            'home' => [
                'title' => 'Argento Force',
                'identifier' => 'home',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => $this->renderContent('page/home.phtml')
            ],
            'typography' => [
                'title' => 'Typography',
                'identifier' => 'typography',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '<body><referenceBlock name="breadcrumbs" remove="true"/></body>',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => $this->renderContent('page/typography.phtml')
            ],
            'terms-and-conditions' => [
                'title' => 'Terms and Conditions',
                'identifier' => 'terms-and-conditions',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '<body><referenceBlock name="breadcrumbs" remove="true"/></body>',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => $this->renderContent('page/terms-and-conditions.phtml')
            ],
            'find-store' => [
                'title' => 'Find our Store',
                'identifier' => 'find-store',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '<body><referenceBlock name="breadcrumbs" remove="true"/></body>',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => $this->renderContent('page/find-store.phtml')
            ],
        ];
    }

    public function getEasyslide()
    {
        return [
            [
                'identifier' => 'argento_force',
                'title'      => 'Argento Force',
                'speed' => 1000,
                'pagination' => '0',
                'navigation' => '1',
                'scrollbar' => '0',
                'autoplay' => 5000,
                'effect' => 'slide',
                'spaceBetween' => 4,
                'startRandomSlide' => '1',
                'lazy' => '1',
                'loadPrevNext' => '1',
                'sizes' => [
                    'sizes' => [
                        [
                            'media_query' => '(max-width: 480px)',
                            'image_width' => '480'
                        ],
                        [
                            'media_query' => '(max-width: 768px)',
                            'image_width' => '768'
                        ]
                    ]
                ],
                'is_active' => 1,
                'slides' => [
                    [
                        'image' => 'argento/force/argento_force_slider1.jpg',
                        'title' => 'Adventure Starts Now',
                        'url' => 'women.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'description' => $this->renderContent('block/easyslide/slide1.phtml'),
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/force/argento_force_slider2.jpg',
                        'title' => 'New Running Collection',
                        'url' => 'men/bottoms-men/shorts-men.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'description' => $this->renderContent('block/easyslide/slide2.phtml'),
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/force/argento_force_slider3.jpg',
                        'title' => 'Get Ready for Action',
                        'url' => 'women/tops-women/tees-women.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'description' => $this->renderContent('block/easyslide/slide3.phtml'),
                        'sort_order' => 30
                    ]
                ]
            ]
        ];
    }

    public function getEasytabs()
    {
        return [
            [
                'title' => 'Questions',
                'alias' => 'questions',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 99,
                'status' => 1,
                'widget_tab' => 0,
                'widget_template' => 'template.phtml',
                'widget_unset' => 'askit_listing,askit_form',
                'widget_block' => 'Swissup\Askit\Block\Question\Widget'
            ],
            [
                'title' => '{{eval code="getSamplesTitle()"}}',
                'alias' => 'samples',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 4,
                'status' => 1,
                'widget_tab' => 0,
                'widget_template' => 'Magento_Downloadable::catalog/product/samples.phtml',
                'widget_unset' => 'product.info.downloadable.samples',
                'widget_block' => 'Magento\Downloadable\Block\Catalog\Product\Samples',
                'conditions_serialized' => '{"type":"Swissup\\\\Easytabs\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Swissup\\\\Easytabs\\\\Model\\\\Rule\\\\Condition\\\\General","attribute":"product_type","operator":"==","value":"downloadable","is_value_processed":false}]}'
            ],
            [
                'title' => 'More Info',
                'alias' => 'additional',
                'block' => 'Magento\Catalog\Block\Product\View\Attributes',
                'sort_order' => 10,
                'status' => 1,
                'widget_tab' => 0,
                'widget_template' => 'Magento_Catalog::product/view/attributes.phtml'
            ],
            [
                'title' => 'Bought Together',
                'alias' => 'bought.together',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 20,
                'status' => 1,
                'widget_tab' => 0,
                'widget_template' => 'Swissup_SoldTogether::product/order.phtml',
                'widget_unset' => 'soldtogether.product.order',
                'widget_block' => 'Swissup\SoldTogether\Block\Order'
            ]
        ];
    }

    public function getEasybanner()
    {
        return [
            [
                'name' => 'argento_force_home',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'new-collection',
                        'title'      => 'New Collection',
                        'url'        => 'highlight/new.html',
                        'image'      => '/argento/force/home-banner.png',
                        'width'      => 0,
                        'height'     => 0,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ]
                ]
            ]
        ];
    }

    public function getProductAttribute()
    {
        return [];
    }

    public function getNavigationpro()
    {
        return [
            [
                'activate' => 1,
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_MEGAMENU,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-force'),
                'settings' => [
                    'css_class' => 'navpro-nowrap justify-center navpro-nowrap-offset-100',
                    'identifier' => 'argento_force_topmenu',
                    'dropdown_settings' => [
                        'level1' => [
                            'layout' => [
                                [
                                    'type' => 'children',
                                    'size' => 9,
                                ],
                                [
                                    'type' => 'html',
                                    'size' => 3,
                                    'display_mode' => 'if_has_children',
                                    'content' => <<<HTML
<div class="xs-hide sm-hide" style="position:relative;max-height:310px;overflow:hidden;margin:10px 0;">
    <img alt="Bestsellers"
        src="{{media url='wysiwyg/navigation/bestsellers-300.jpg'}}"
        srcset="{{media url='wysiwyg/navigation/bestsellers-300.jpg'}} 1x,
                {{media url='wysiwyg/navigation/bestsellers-600.jpg'}} 2x"
    />
    <div style="position:absolute;top:0;left:0;width:100%;padding:10px 15px;box-sizing:border-box;">
        <p class="h3" style="margin-top:0;color:#fff;">Bestsellers</p>
        <a href="{{store direct_url='highlight/bestsellers.html'}}" class="button action primary">Shop Now</a>
    </div>
</div>
HTML
                                ],
                            ],
                        ],
                        'level2' => [
                            'layout' => [
                                [
                                    'type' => 'children',
                                    'max_children_count' => 4,
                                ],
                            ],
                        ],
                    ],
                    'item_settings' => [
                        'level2' => [
                            'html' => <<<HTML
<a href="{{navpro data='url'}}" class="{{navpro data='class'}}{{depend remote_entity.thumbnail}} navpro-a-with-thumbnail{{/depend}}">
    <span>{{navpro data="name"}}</span>
    {{depend remote_entity.thumbnail}}
        <img class="xs-hide sm-hide" src="{{media url=''}}/catalog/category/{{navpro data='remote_entity.thumbnail'}}" />
    {{/depend}}
</a>
HTML
                        ],
                    ],
                ],
                'items' => [
                    'home' => false,
                    'contacts' => false,
                ]
            ]
        ];
    }

    /**
     * Build Prolabel text
     *
     * @param  string $mode product|category
     * @param  string $text
     * @return string
     */
    private function _getProlabelText(
        $mode,
        $text = ''
    ) {
        $map = [
            'product' => [
                'padding' => '2px 8px 0 10px',
                'lineHeight' => '34px'
            ],
            'category' => [
                'padding' => '2px 6px 0 8px',
                'lineHeight' => '26px'
            ]
        ];
        $item = $map[$mode];

        return "<b style=\"display: block; border: 2px solid #2B3945; padding: {$item['padding']}; line-height: {$item['lineHeight']}; margin: -5px 0px 0px -5px;\">{$text}</b>";
    }

    /**
     * Build Prolabel custom styles
     *
     * @param  string $mode product|category
     * @param  string $background
     * @param  string $fontSize
     * @param  string $margin
     * @return string
     */
    private function _getProlabelCustom(
        $mode,
        $background,
        $fontSize = null,
        $margin = null
    ) {
        $map = [
            'product' => [
                'fontSize' => '20px',
                'margin' => '20px 16px 16px 20px'
            ],
            'category' => [
                'fontSize' => '14px',
                'margin' => '12px 8px 8px 12px'
            ]
        ];
        $item = $map[$mode];
        if ($fontSize) {
            $item['fontSize'] = $fontSize;
        }

        if ($margin) {
            $item['margin'] = $margin;
        }

        return "background: {$background};\n"
            .  "box-shadow: 0px 0px 0px 2px {$background};\n"
            .  "color: #2B3945;\n"
            .  "font-size: {$item['fontSize']};\n"
            .  "font-family: sans-serif;\n"
            .  "margin: {$item['margin']};\n"
            .  "text-shadow: 1px 1px 0 rgba(255, 255, 255, .5);";
    }
}
