<?php

namespace Swissup\ThemeFrontendArgentoPure2\Upgrades;

class InitialInstallation extends \Swissup\Core\Model\Module\Upgrade
{
    public function getCommands()
    {
        return [
            'Configuration' => $this->getConfiguration(),
            'ConfigurationReplacement' => $this->getConfigurationReplacement(),
            'CmsBlock'      => $this->getCmsBlocks(),
            'CmsPage'       => $this->getCmsPages(),
            'Easyslide'     => $this->getEasyslide(),
            'Easytabs'      => $this->getEasytabs(),
            'Easybanner'    => $this->getEasybanner(),
            'ProductAttribute' => $this->getProductAttribute(),
            'Products' => [
                'news_from_date' => 6,
                'recommended' => 6,
            ],
            'Navigationpro' => $this->getNavigationpro(),
        ];
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
        return [
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-pure2'),

            'cms/wysiwyg/enabled' => 'hidden',

            'ajaxsearch/folded/enable' => 0,

            'fblike/product/enabled' => 1,

            'lightboxpro/popup/type' => 'advanced',

            'prolabels/general/base' => '.fotorama__stage',
            'prolabels/on_sale/product/active' => 1,
            'prolabels/on_sale/category/active' => 1,
            'prolabels/is_new/product/active' => 1,
            'prolabels/is_new/category/active' => 1,

            'reviewreminder/general/enabled' => 1,

            'richsnippets/breadcrumbs/enabled' => 1,

            'soldtogether/order/enabled' => 1,
            'soldtogether/order/count' => 4,
            'soldtogether/customer/enabled' => 1,
            'soldtogether/customer/count' => 4
        ];
    }

    public function getCmsBlocks()
    {
        return [
            'header_cms_links' => [
                'title' => 'header_cms_links',
                'identifier' => 'header_cms_links',
                'is_active' => 1,
                'content' => <<<HTML
<ul class="header links header-cms-links">
    <li class="first"><a href="{{store url='support'}}">support</a></li>
    <li><a href="{{store url='faq'}}">faq</a></li>
    <li class="last"><a href="{{store url='knowledgebase'}}">knowledge base</a></li>
</ul>
HTML
            ],
            'header_cms_content' => [
                'title' => 'header_cms_content',
                'identifier' => 'header_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="header-cms-content"></div>
HTML
            ],
            'product_sidebar' => [
                'title' => 'product_sidebar',
                'identifier' => 'product_sidebar',
                'is_active' => 1,
                'content' => <<<HTML
<div class="block block-product-sidebar">
    {{widget type="Swissup\Attributepages\Block\Product\Option" attribute_code="brand" css_class="hidden-xs hidden-label a-center" use_link="1" use_image="1" image_type="image" width="200" height="120" block_template="Swissup_Attributepages::product/options.phtml"}}
    {{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="services_sidebar"}}
</div>
HTML
            ],
            'services_sidebar' => [
                'title' => 'services_sidebar',
                'identifier' => 'services_sidebar',
                'is_active' => 1,
                'content' => <<<HTML
<div class="block block-services-sidebar">
    <div class="block-title"><strong>Our Services</strong></div>
    <div class="block-content">
        <div class="icon-section section-delivery">
            <span class="fa-stack fa-2x icon">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-truck fa-stack-1x fa-inverse"></i>
            </span>
            <div class="section-info">
                <div class="h4 section-title">Delivery</div>
                <p>We guarantee to ship your order next day after order has been submitted</p>
            </div>
        </div>
        <div class="icon-section section-customer-service">
            <span class="fa-stack fa-2x icon">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-users fa-stack-1x fa-inverse"></i>
            </span>
            <div class="section-info">
                <div class="h4 section-title">Customer Service</div>
                <p>Please contacts us and our customer service team will answer all your questions</p>
            </div>
        </div>
        <div class="icon-section section-returns">
            <span class="fa-stack fa-2x icon">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-reply fa-stack-1x fa-inverse"></i>
            </span>
            <div class="section-info">
                <div class="h4 section-title">Easy Returns</div>
                <p>If you are not satisfied with your order - send it back within  30 days after day of purchase!</p>
            </div>
        </div>
    </div>
</div>
HTML
            ],
            'footer_cms_content' => [
                'title' => 'footer_cms_content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="block-social">
    <div class="social-icons colorize-fa-stack-hover">
        <a href="https://facebook.com/" class="icon icon-facebook">
            <span class="fa-stack">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="https://twitter.com/" class="icon icon-twitter">
            <span class="fa-stack">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="https://youtube.com/" class="icon icon-youtube">
            <span class="fa-stack">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-youtube-play fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="{{store url='rss'}}" class="icon icon-rss">
            <span class="fa-stack">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-rss fa-stack-1x fa-inverse"></i>
            </span>
        </a>
    </div>
</div>
<div class="argento-grid">
    <div class="col-md-5 col-sm-12">
        <div class="h4">Company Information</div>
        <ul class="footer links argento-grid">
            <li class="col-md-4 col-xs-4">
                <ul>
                    <li><a href="{{store direct_url='blog'}}">Blog</a></li>
                    <li><a href="{{store direct_url='wishlist'}}">Wishlist</a></li>
                    <li><a href="{{store direct_url='terms'}}">Terms of Use</a></li>
                    <li><a href="{{store direct_url='carriers'}}">Carriers</a></li>
                </ul>
            </li>
            <li class="col-md-4 col-xs-4">
                <ul>
                    <li><a href="{{store direct_url='location'}}">Store location</a></li>
                    <li><a href="{{store direct_url='privacy'}}">Privacy policy</a></li>
                    <li><a href="{{store direct_url='our-company'}}">Our company</a></li>
                    <li><a href="{{store direct_url='about'}}">About Us</a></li>
                </ul>
            </li>
            <li class="col-md-4 col-xs-4">
                <ul>
                    <li><a href="{{store direct_url='sales/order/history'}}">Order Status</a></li>
                    <li><a href="{{store direct_url='customer/account'}}">My Account</a></li>
                    <li><a href="{{store direct_url='exchanges'}}">Returns and Exchanges</a></li>
                    <li><a href="{{store direct_url='shipping'}}">Shipping</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="col-md-3 col-sm-6 footer-contacts">
        <div class="h4">Call Us</div>
        <a class="footer-action" href="tel:1.800.555.1903" title="Call us on 1.800.555.1903">1.800.555.1903</a>
        <p>We're available 24/7. Please note the more accurate the information you can provide us with the quicker we can respond to your query.</p>
    </div>

    <div class="col-md-4 col-sm-6 footer-newsletter">
        <div class="h4">Newsletter</div>
        {{block class="Magento\Newsletter\Block\Subscribe" template="subscribe.phtml"}}
        <p>Subscribe to our free e-newsletter, and get new tips every two weeks.</p>
    </div>
</div>
<div class="a-center">
    <img width="453" height="50" style="margin: 12px 0 10px;"
        src="{{view url='images/payments.png'}}"
        srcset="{{view url='images/payments.png'}} 1x, {{view url='images/payments@2x.png'}} 2x"
        alt="Credit cards, we accept"
    />
</div>

HTML
            ]
        ];
    }

    public function getCmsPages()
    {
        return [
            'home' => [
                'title' => 'Argento Pure2',
                'identifier' => 'home',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => <<<HTML
<div class="jumbotron jumbotron-image no-padding">
    {{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_pure2"}}
</div>

<div class="jumbotron">
    <div class="container">
        <div class="block block-subcategories">
            <div class="block-title">
                <strong>The Essentials</strong>
            </div>
            {{widget type="Swissup\Easycatalogimg\Block\Widget\SubcategoriesList" category_count="4" subcategory_count="5" column_count="4" show_image="1" image_width="200" image_height="200" template="Swissup_Easycatalogimg::list.phtml"}}
        </div>
    </div>
</div>

<div class="jumbotron">
    <div class="container argento-tabs" data-mage-init='{"argentoTabs": {}}'>
        {{widget type="Swissup\Highlight\Block\ProductList\NewList" title="New Products" products_count="6" column_count="3" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_title="Shop New"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Onsale" title="Special Offer" products_count="6" column_count="3" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_title="Shop Sale"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Bestsellers" title="Bestsellers" products_count="6" column_count="3" template="Swissup_Highlight::product/list.phtml" mode="grid" period="P6M" show_page_link="1" page_link_title="Shop Bestsellers" min_popularity="1"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Popular" title="Popular" products_count="6" column_count="3" template="Swissup_Highlight::product/list.phtml" mode="grid" period="P6M" show_page_link="1" page_link_title="Shop Popular" min_popularity="1"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Attribute\Yesno" title="Editor's Choice" attribute_code="recommended" products_count="6" column_count="3" order="default" dir="asc" template="Swissup_Highlight::product/list.phtml" mode="grid"}}
    </div>
</div>

<div class="jumbotron">
    <div class="container">
        <div class="block widget block-carousel">
            <div class="block-title">
                <strong>Our Brands</strong>
            </div>
            <div class="block-content">
                <div class="slick-slider" data-mage-init='{"slick": {"slidesToShow": 7, "slidesToScroll": 1, "dots": false, "autoplay": true, "variableWidth": true, "swipeToSlide": true, "rows": 0, "lazyLoad": "ondemand"}}'>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/gucci.jpg'}}" alt="" width="150" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/lv.jpg'}}" alt="" width="100" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/ck.jpg'}}" alt="" width="130" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/chanel.jpg'}}" alt="" width="170" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/guess.jpg'}}" alt="" width="130" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/versace.jpg'}}" alt="" width="145" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/gucci.jpg'}}" alt="" width="150" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/lv.jpg'}}" alt="" width="100" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/ck.jpg'}}" alt="" width="130" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/chanel.jpg'}}" alt="" width="170" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/guess.jpg'}}" alt="" width="130" height="80"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/versace.jpg'}}" alt="" width="145" height="80"/></a>
                </div>
            </div>
        </div>

        <div class="a-center">
            {{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_pure2_home_wide"}}
        </div>
    </div>
</div>
HTML
            ]
        ];
    }

    public function getEasyslide()
    {
        return [
            [
                'identifier' => 'argento_pure2',
                'title'      => 'Argento Pure2',
                'theme' => 'black',
                'speed' => 1000,
                'pagination' => '1',
                'navigation' => '0',
                'scrollbar' => '0',
                'autoplay' => 3000,
                'effect' => 'slide',
                'lazy' => '1',
                'loadPrevNext' => '0',
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
                'is_active' => '1',
                'slides' => [
                    [
                        'image' => 'argento/pure2/argento_pure2_slide_1.jpg',
                        'title' => 'Slide 1',
                        'description' => '',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/pure2/argento_pure2_slide_2.jpg',
                        'title' => 'Slide 2',
                        'description' => '',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/pure2/argento_pure2_slide_3.jpg',
                        'title' => 'Slide 3',
                        'description' => '',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
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

    public function getEasybanner()
    {
        return [
            [
                'name' => 'argento_pure2_home_wide',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'free_ground_delivery',
                        'title'      => 'free_ground_delivery',
                        'url'        => 'free-ground-delivery',
                        'image'      => '/argento/pure2/argento_pure2_callout_home1.png',
                        'width'      => 1019,
                        'height'     => 100,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ]
                ]
            ]
        ];
    }

    public function getProductAttribute()
    {
        return [
            [
                'attribute_code' => 'recommended',
                'frontend_label' => ['Recommended'],
                'default_value'  => 0
            ]
        ];
    }

    public function getNavigationpro()
    {
        return [
            [
                'activate' => 1,
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_MEGAMENU,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-pure2'),
                'settings' => [
                    'css_class' => 'navpro-nowrap',
                    'identifier' => 'argento_pure2_topmenu',
                ],
            ]
        ];
    }
}
