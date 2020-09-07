<?php

namespace Swissup\ThemeFrontendArgentoEssence\Upgrades;

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
            'ProductAttribute' => $this->getProductAttribute(),
            'Easybanner'    => $this->getEasybanner(),
            'Products' => [
                'coming_soon'    => 6,
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
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-essence'),

            'cms/wysiwyg/enabled' => 'hidden',

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
<div class="header-cms-content">
    <img src="{{view url='images/callouts/callout_customer_support.gif'}}" alt="Free calls: 1.800.555.1903" width="160" height="60"/>
</div>
HTML
            ],
            'footer_cms_content' => [
                'title' => 'footer_cms_content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="argento-grid">
    <div class="col-md-9">
        <ul class="footer links argento-grid">
            <li class="col-md-3 col-xs-6">
                <div class="h4">About us</div>
                <ul>
                    <li><a href="{{store direct_url='about'}}">About Us</a></li>
                    <li><a href="{{store direct_url='our-company'}}">Our company</a></li>
                    <li><a href="{{store direct_url='carriers'}}">Carriers</a></li>
                    <li><a href="{{store direct_url='shipping'}}">Shipping</a></li>
                </ul>
            </li>
            <li class="col-md-3 col-xs-6">
                <div class="h4">Customer center</div>
                <ul>
                    <li><a href="{{store direct_url='customer/account'}}">My Account</a></li>
                    <li><a href="{{store direct_url='sales/order/history'}}">Order Status</a></li>
                    <li><a href="{{store direct_url='wishlist'}}">Wishlist</a></li>
                    <li><a href="{{store direct_url='exchanges'}}">Returns and Exchanges</a></li>
                </ul>
            </li>
            <li class="col-md-3 col-xs-6">
                <div class="h4">Info</div>
                <ul>
                    <li><a href="{{store direct_url='privacy'}}">Privacy policy</a></li>
                    <li><a href="{{store direct_url='delivery'}}">Delivery information</a></li>
                    <li><a href="{{store direct_url='returns'}}">Returns policy</a></li>
                </ul>
            </li>
            <li class="col-md-3 col-xs-6">
                <div class="h4">Contacts</div>
                <ul>
                    <li><a href="{{store direct_url='contact'}}">Contact Us</a></li>
                    <li><a href="{{store direct_url='location'}}">Store location</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="col-md-3 footer-contacts">
        <div class="h4">Visit Argento Store</div>
        <address style="margin-bottom: 10px;">
            221B Baker Street<br>
            West Windsor, NJ 08550<br>
            <strong>1.800.555.1903</strong><br>
        </address>
        <a href="{{store direct_url='map'}}" title="Show map">get directions</a><br>
        <img width="200" height="60" style="margin-top: 10px;"
            src="{{view url='images/security_sign.gif'}}"
            srcset="{{view url='images/security_sign@2x.gif'}} 2x"
            alt="Security Seal"
        />
    </div>
</div>
HTML
            ],
            'footer_social_icons' => [
                'title' => 'footer_social_icons',
                'identifier' => 'footer_social_icons',
                'status' => 1,
                'content' => <<<HTML
<div class="social-icons colorize-fa-stack-hover">
  Join Our Community
  <a href="https://facebook.com/" class="icon icon-facebook">
    <span class="fa-stack">
      <i class="fa fa-square fa-stack-2x"></i>
      <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
    </span>
  </a>
  <a href="https://twitter.com/" class="icon icon-twitter">
    <span class="fa-stack">
      <i class="fa fa-square fa-stack-2x"></i>
      <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
    </span>
  </a>
  <a href="https://youtube.com/" class="icon icon-youtube">
    <span class="fa-stack">
      <i class="fa fa-square fa-stack-2x"></i>
      <i class="fa fa-youtube-play fa-stack-1x fa-inverse"></i>
    </span>
  </a>
  <a href="{{store url='rss'}}" class="icon icon-rss">
    <span class="fa-stack">
      <i class="fa fa-square fa-stack-2x"></i>
      <i class="fa fa-rss fa-stack-1x fa-inverse"></i>
    </span>
  </a>
</div>
HTML
            ]
        ];
    }

    public function getCmsPages()
    {
        return [
            'home' => [
                'title' => 'Argento Essence',
                'identifier' => 'home',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => <<<HTML
<div class="argento-grid row">
    <div class="col-md-8">
        {{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_essence"}}
    </div>
    <div class="col-md-4">
        {{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_essence_home_top" banner_css_class="col-xs-4 col-md-12" additional_css_class="argento-grid"}}
    </div>
</div>

<div class="row">{{widget type="Swissup\Easycatalogimg\Block\Widget\SubcategoriesList" category_count="4" subcategory_count="5" column_count="4" show_image="1" image_width="200" image_height="200" template="Swissup_Easycatalogimg::list.phtml"}}</div>

<div class="row">{{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_essence_home_wide"}}</div>

<div class="argento-grid row block-products-promo">
    <div class="blocks-main item col-md-8 argento-tabs" data-mage-init='{"argentoTabs": {}}'>
        {{widget type="Swissup\Highlight\Block\ProductList\NewList" title="New Products" products_count="6" column_count="3" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_title="View All New Products"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Onsale" title="Special Offers" products_count="6" column_count="3" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_title="View All On Sale Products"}}
        {{widget type="Swissup\Highlight\Block\ProductList\Attribute\Yesno" title="Coming soon" attribute_code="coming_soon" products_count="6" column_count="3" order="default" dir="asc" template="Swissup_Highlight::product/list.phtml" mode="grid"}}
    </div>
    <div class="sidebar blocks-secondary col-md-4">
        <div class="argento-grid">
            <div class="col-md-12 col-sm-12">{{widget type="Swissup\Testimonials\Block\Widgets\SideReview"}}</div>
            <div class="col-md-12 col-sm-6">{{widget type="Swissup\Highlight\Block\ProductList\Bestsellers" title="Bestsellers" products_count="2" template="product/widget/column/list.phtml" period="P6M" show_page_link="1" page_link_title="View All Bestsellers" min_popularity="1"}}</div>
            <div class="col-md-12 col-sm-6">{{widget type="Swissup\Highlight\Block\ProductList\Popular" title="Popular Products" products_count="2" template="product/widget/column/list.phtml" period="P6M" show_page_link="1" page_link_title="View All Popular Products" min_popularity="1"}}</div>
        </div>
    </div>
</div>

<div class="block row widget block-promo block-carousel">
    <div class="block-title">
        <strong>Featured Brands</strong>
    </div>
    <div class="block-content">
        <div class="slick-slider" data-mage-init='{"slick": {"slidesToShow": 6, "slidesToScroll": 1, "dots": false, "autoplay": true, "variableWidth": true, "swipeToSlide": true, "rows": 0, "lazyLoad": "ondemand"}}'>
            <div><a href="#"><img data-lazy="{{view url='images/brands/sony.jpg'}}" alt="" width="128" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/apple.jpg'}}" alt="" width="70" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/garmin.jpg'}}" alt="" width="154" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/htc.jpg'}}" alt="" width="124" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/intel.jpg'}}" alt="" width="103" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/motorola.jpg'}}" alt="" width="204" height="76"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/sony.jpg'}}" alt="" width="128" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/apple.jpg'}}" alt="" width="70" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/garmin.jpg'}}" alt="" width="154" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/htc.jpg'}}" alt="" width="124" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/intel.jpg'}}" alt="" width="103" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/motorola.jpg'}}" alt="" width="204" height="76"/></a></div>
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
                'identifier' => 'argento_essence',
                'title'      => 'Argento Essence',
                'speed' => 1000,
                'pagination' => '1',
                'navigation' => '0',
                'scrollbar' => '0',
                'autoplay' => 3000,
                'effect' => 'slide',
                'lazy' => '1',
                'loadPrevNext' => '0',
                'is_active' => '1',
                'slides' => [
                    [
                        'image' => 'argento/essence/argento_essence_slide_1.png',
                        'title' => 'Slide 1',
                        'description' => 'Sony VAIO T Series Laptop',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/essence/argento_essence_slide_2.png',
                        'title' => 'Slide 2',
                        'description' => 'Sony VAIO T Series Laptop',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/essence/argento_essence_slide_3.png',
                        'title' => 'Slide 3',
                        'description' => 'Sony VAIO T Series Laptop',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 30
                    ],
                    [
                        'image' => 'argento/essence/argento_essence_slide_4.png',
                        'title' => 'Slide 4',
                        'description' => 'Sony VAIO T Series Laptop',
                        'desc_position' => 'bottom',
                        'desc_background' => 'transparent',
                        'sort_order' => 40
                    ]
                ]
            ]
        ];
    }

    public function getEasybanner()
    {
        return [
            [
                'name' => 'argento_essence_home_top',
                'limit' => 3,
                'banners' => [
                    [
                        'identifier' => 'ups-delivery',
                        'title'      => 'Ups home delivery',
                        'url'        => 'ups-delivery',
                        'image'      => '/argento/essence/argento_essence_ups_delivery.png',
                        'width'      => 311,
                        'height'     => 110,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                    [
                        'identifier' => 'galaxy-s3',
                        'title'      => 'Samsung Galaxy S3',
                        'url'        => 'galaxy-s3',
                        'image'      => '/argento/essence/argento_essence_galaxy_s3.png',
                        'width'      => 311,
                        'height'     => 110,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                    [
                        'identifier' => 'roku2-xs',
                        'title'      => 'Roku 2 XS Streamin Player',
                        'url'        => 'roku2-xs',
                        'image'      => '/argento/essence/argento_essence_roku2_xs.png',
                        'width'      => 311,
                        'height'     => 110,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                ]
            ],
            [
                'name' => 'argento_essence_home_wide',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'hp-envy-17',
                        'title'      => 'HP Envy 17',
                        'url'        => 'hp-envy-17',
                        'image'      => '/argento/essence/argento_essence_hp_envy_17.png',
                        'width'      => 960,
                        'height'     => 120,
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
                'attribute_code' => 'coming_soon',
                'frontend_label' => ['Coming Soon'],
                'default_value'  => 0
            ]
        ];
    }

    public function getNavigationpro()
    {
        return [
            [
                'activate' => 1,
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_SIMPLE,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-essence'),
                'settings' => [
                    'css_class' => 'navpro-nowrap',
                    'identifier' => 'argento_essence_topmenu',
                ],
            ]
        ];
    }
}
