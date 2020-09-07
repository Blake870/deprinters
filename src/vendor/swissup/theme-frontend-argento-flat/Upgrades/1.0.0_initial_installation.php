<?php

namespace Swissup\ThemeFrontendArgentoFlat\Upgrades;

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
            'Products' => [
                'news_from_date' => 6
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
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-flat'),

            'cms/wysiwyg/enabled' => 'hidden',

            'ajaxsearch/folded/enable' => 1,

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
    {{widget type="Swissup\Attributepages\Block\Product\Option" attribute_code="brand" css_class="hidden-label a-center" use_link="1" use_image="1" image_type="image" width="200" height="120" block_template="Swissup_Attributepages::product/options.phtml"}}
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
<div class="argento-grid">
    <div class="col-md-4">
        <div class="h4">Company Information</div>
        <ul class="footer links argento-grid">
            <li class="col-md-6 col-xs-6">
                <ul>
                    <li><a href="{{store direct_url='blog'}}">Blog</a></li>
                    <li><a href="{{store direct_url='location'}}">Store location</a></li>
                    <li><a href="{{store direct_url='privacy'}}">Privacy policy</a></li>
                    <li><a href="{{store direct_url='terms'}}">Terms of Use</a></li>
                    <li><a href="{{store direct_url='our-company'}}">Our company</a></li>
                    <li><a href="{{store direct_url='about'}}">About Us</a></li>
                </ul>
            </li>
            <li class="col-md-6 col-xs-6">
                <ul>
                    <li><a href="{{store direct_url='sales/order/history'}}">Order Status</a></li>
                    <li><a href="{{store direct_url='wishlist'}}">Wishlist</a></li>
                    <li><a href="{{store direct_url='customer/account'}}">My Account</a></li>
                    <li><a href="{{store direct_url='exchanges'}}">Returns and Exchanges</a></li>
                    <li><a href="{{store direct_url='carriers'}}">Carriers</a></li>
                    <li><a href="{{store direct_url='shipping'}}">Shipping</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="col-md-4">
        <div class="argento-grid">
            <div class="col-sm-6 col-md-12">
                <div class="block block-social">
                    <div class="h4">Get Social</div>
                    <p>Join our on Facebook and get recent news about our new products and offers.</p>
                    <div class="social-icons colorize-fa-hover">
                        <a href="twitter.com"><i class="fa fa-2x fa-fw fa-twitter"></i></a>
                        <a href="facebook.com"><i class="fa fa-2x fa-fw fa-facebook"></i></a>
                        <a href="youtube.com"><i class="fa fa-2x fa-fw fa-youtube"></i></a>
                        <a href="rss.com"><i class="fa fa-2x fa-fw fa-rss"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-12">
                {{block class="Magento\Newsletter\Block\Subscribe" template="subscribe.phtml"}}
            </div>
        </div>
    </div>

    <div class="col-md-4 footer-contacts">
        <div class="h4">About Us</div>
        <address style="margin-bottom: 10px;">
            221B Baker Street<br>
            West Windsor, NJ 08550<br>
            <strong>1.800.555.1903</strong><br>
        </address>
        <p>Natural Herbs is truly professional company on vitamine and sport nutrition supplements' marketplace. We sell only the highest-grade substances needed for health and bodily growth. Our web-store offers a huge choice of products for better physical wellbeing. Let's engage people to be healthy!</p>
        <img width="302" height="33" style="margin: 5px 0 7px;"
            src="{{view url='images/payments.png'}}"
            srcset="{{view url='images/payments.png'}} 1x, {{view url='images/payments@2x.png'}} 2x"
            alt="Credit cards, we accept"
        />
    </div>
</div>
HTML
            ]
        ];
    }

    public function getCmsPages()
    {
        return [
            'home' => [
                'title' => 'Argento Flat',
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
    <div class="cover cover-pastel"><div class="left triangle"></div><div class="right triangle"></div></div>
    <div class="container">
        {{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_flat"}}
    </div>
</div>

<div class="jumbotron jumbotron-pastel jumbotron-inverse no-padding-top hero">
    <div class="container">
        <div class="row">
            <div class="block-title">
                <strong>Shop Our Store for</strong>
                <p class="subtitle no-margin">more than 25,000 health products including vitamins, herbs, sport supplements, diet and much more!</p>
            </div>
            {{widget type="Swissup\Easycatalogimg\Block\Widget\SubcategoriesList" category_count="4" subcategory_count="1" column_count="4" show_image="1" image_width="200" image_height="200" template="Swissup_Easycatalogimg::list.phtml"}}
        </div>
    </div>
</div>

<div class="jumbotron jumbotron-pastel-alt no-padding">
    <div class="container">
        {{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_flat_home_wide"}}
    </div>
</div>

<div class="jumbotron hero">
    <div class="container">
        {{widget type="Swissup\Highlight\Block\ProductList\NewList" title="New Products" products_count="4" column_count="4" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_position="top" page_link_title="Browse all new products at our store &raquo;"}}
    </div>
</div>

<div class="jumbotron jumbotron-pattern hero">
    <div class="cover"><div class="left triangle"></div><div class="right triangle"></div></div>
    <div class="stub"></div>
    <div class="container">
        {{widget type="Swissup\Highlight\Block\ProductList\Onsale" title="Special Offer" products_count="4" column_count="4" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_position="top" page_link_title="Browse all on sale products at out store &raquo;"}}
    </div>
</div>

<div class="jumbotron hero no-padding-top">
    <div class="container">
        {{widget type="Swissup\Highlight\Block\ProductList\Bestsellers" title="Bestsellers" products_count="4" column_count="4" template="Swissup_Highlight::product/list.phtml" mode="grid" period="P6M" show_page_link="1" page_link_position="top" page_link_title="Browse all bestseller products at our store &raquo;" min_popularity="1"}}
    </div>
</div>

<div class="jumbotron hero">
    <div class="stub"></div>
    <div class="container">
        <div class="block block-benefits">
            <div class="block-title wow fadeInDown" data-wow-duration="0.5s"><strong>Why choose us</strong></div>
            <div class="block-content argento-grid">
                <div class="col-md-3 wow bounceInLeft" data-wow-delay="0.2s">
                    <span class="fa-stack fa-4x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-tags fa-stack-1x fa-inverse"></i></span>
                    <h3>Low Pricing</h3>
                    <p>Meet all types for your body's needs, that are healthy for you and for your pocket. Click for big savings.</p>
                </div>
                <div class="col-md-3 wow bounceInLeft">
                    <span class="fa-stack fa-4x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-cubes fa-stack-1x fa-inverse"></i></span>
                    <h3>Huge Selection</h3>
                    <p>Make your healthy choice using the huge variety of vitamins and sports nutrition. Let your transformation go on.</p>
                </div>
                <div class="col-md-3 wow bounceInRight">
                    <span class="fa-stack fa-4x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-birthday-cake fa-stack-1x fa-inverse"></i></span>
                    <h3>Reward Points</h3>
                    <p>Get reward points by boosting your healthy activity online. Stay with us and gain more.</p>
                </div>
                <div class="col-md-3 wow bounceInRight" data-wow-delay="0.2s">
                    <span class="fa-stack fa-4x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-comments fa-stack-1x fa-inverse"></i></span>
                    <h3>Ask Experts</h3>
                    <p>Have a question? Ask an expert and get complete online support. We are open for you.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron jumbotron-bright jumbotron-inverse hero">
    <div class="stub"></div>
    <div class="container">
        <div class="block block-about wow fadeIn"  data-wow-delay="0.2s">
            <div class="block-title"><strong>About us</strong></div>
            <div class="block-content">
                <p>
                    Natural Herbs company was found with idea to ensure users more natural healthy care.
                    The company is making name for itself as an advanced store with reliable service. Our
                    online store works with leaders worldwide producing vitamins, herbs and sport nutrition
                    supplements. We provide high-quality products that suit your needs and fit your budget.
                </p>
                <p>
                    Natural Herbs is aiming to become your full-service friend. We focus on keeping you motivated
                    improve your health. Build your own body with us! We'll help you to reach your goal.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron hero">
    <div class="stub"></div>
    <div class="cover cover-dark"><div class="left triangle"></div><div class="right triangle"></div></div>
    <div class="container">
        <div class="block row widget block-carousel">
            <div class="block-title">
                <strong>Popular Brands</strong>
                <p class="subtitle">check most trusted brands from more then 50 leading manufactures presented at our store.</p>
            </div>
            <div class="block-content">
                <div class="slick-slider" data-mage-init='{"slick": {"slidesToShow": 6, "slidesToScroll": 1, "dots": false, "autoplay": true, "variableWidth": true, "swipeToSlide": true, "rows": 0, "lazyLoad": "ondemand"}}'>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/life_extension.gif'}}" alt="Life Extension"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/gnc.gif'}}" alt="GNC"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/mega_food.gif'}}" alt="Mega Food" /></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/nordic_naturals.gif'}}" alt="Nordic Naturals"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/life_extension.gif'}}" alt="Life Extension"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/gnc.gif'}}" alt="GNC"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/mega_food.gif'}}" alt="Mega Food"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/nordic_naturals.gif'}}" alt="Nordic Naturals"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/life_extension.gif'}}" alt="Life Extension"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/gnc.gif'}}" alt="GNC"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/mega_food.gif'}}" alt="Mega Food"/></a>
                    <a href="#"><img data-lazy="{{view url='images/catalog/brands/nordic_naturals.gif'}}" alt="Nordic Naturals"/></a>
                </div>
            </div>
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
                'identifier' => 'argento_flat',
                'title'      => 'Argento Flat',
                'theme' => 'white',
                'speed' => 1000,
                'pagination' => '0',
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
                'is_active' => 1,
                'slides' => [
                    [
                        'image' => 'argento/flat/argento_flat_slide_1.png',
                        'title' => 'Slide 1',
                        'description' => '',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/flat/argento_flat_slide_2.png',
                        'title' => 'Slide 2',
                        'description' => '',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/flat/argento_flat_slide_3.png',
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
                'name' => 'argento_flat_home_wide',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'flat_rate_shipping_on_all_products',
                        'title'      => 'flat_rate_shipping_on_all_products',
                        'url'        => 'flat-rate-shipping-on-all-products',
                        'image'      => '/argento/flat/argento_flat_special_offer.png',
                        'width'      => 1160,
                        'height'     => 130,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ]
                ]
            ]
        ];
    }

    public function getNavigationpro()
    {
        return [
            [
                'activate' => 1,
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_SIMPLE,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-flat'),
                'settings' => [
                    'css_class' => 'navpro-nowrap navpro-nowrap-offset-100',
                    'identifier' => 'argento_flat_topmenu',
                ],
            ]
        ];
    }
}
