<?php

namespace Swissup\ThemeFrontendArgentoMall\Upgrades;

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
                'news_from_date' => 10,
                'recommended' => 1,
                'featured' => 6
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
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-mall'),

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
            'footer_cms_content' => [
                'title' => 'footer_cms_content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="argento-grid">
    <div class="col-sm-4">
        <div class="footer-links-cms">
            <div class="h4"><span>Informational</span></div>
            <ul class="argento-grid">
                <li class="col-md-6">
                    <ul>
                        <li><a href="{{store direct_url='about'}}">About Us</a></li>
                        <li><a href="{{store direct_url='our-company'}}">Our company</a></li>
                        <li><a href="{{store direct_url='press'}}">Press</a></li>
                        <li><a href="{{store direct_url='contact'}}">Contact Us</a></li>
                        <li><a href="{{store direct_url='location'}}">Store location</a></li>
                    </ul>
                </li>
                <li class="last col-md-6">
                    <ul>
                        <li><a href="{{store direct_url='privacy'}}">Privacy policy</a></li>
                        <li><a href="{{store direct_url='delivery'}}">Delivery information</a></li>
                        <li><a href="{{store direct_url='returns'}}">Returns policy</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-sm-4">
        {{widget type="Magento\Reports\Block\Product\Widget\Viewed" page_size="5" template="widget/viewed/column/viewed_list_footer.phtml"}}
    </div>
    <div class="col-sm-4 footer-contacts">
        <div class="h4">Visit Argento Store</div>
        <address style="margin-bottom: 10px;">
            221B Baker Street<br>
            West Windsor, NJ 08550<br>
            <strong>1.800.555.1903</strong><br>
        </address>
        <a href="{{store direct_url='map'}}" title="Show map">get directions</a><br>
        <img width="199" height="56" style="margin-top: 10px;"
            src="{{view url='images/security_sign.gif'}}"
            srcset="{{view url='images/security_sign@2x.gif'}} 2x"
            alt="Security Seal"
        />
    </div>
</div>
HTML
            ],
            'video_of_the_day' => [
                'title' => 'Video of the Day',
                'identifier' => 'video_of_the_day',
                'is_active' => 1,
                'content' => <<<HTML
<div class="block block-alt video-of-day">
    <div class="block-title"><strong role="heading" aria-level="2">Video of the day</strong></div>
    <div class="block-content">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/6BQfCoqbubE?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
        </div>
        <p><small>Amazing Canon Rebel XSi commercial that I saw on TV the other day.</small></p>
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
                'title' => 'Argento Mall',
                'identifier' => 'home',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => <<<HTML
<div class="argento-grid row callout-home-top">
    <div class="col-md-9">{{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_mall"}}</div>
    <div class="col-md-3 hidden-xs">{{block class="Magento\Newsletter\Block\Subscribe" template="subscribe.phtml"}} {{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_mall_home_top"}}</div>
</div>
<div class="argento-grid row col-home-set">
    <div class="col-lg-3 visible-lg-block visible-md-block sidebar">{{block class="Magento\Theme\Block\Html\Topmenu" name="nav-homepage-left" before="-" template="Magento_Theme::html/home-menu-left.phtml"}}</div>
    <div class="col-lg-9 col-md-12">
        <div class="argento-grid">
            <div class="col-md-4 col-sm-4 col-xs-6">{{widget type="Swissup\Highlight\Block\ProductList\Onsale" title="Deal of the week" products_count="1" column_count="1" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid"}}</div>
            <div class="col-md-4 col-sm-4 col-xs-6">{{widget type="Swissup\Highlight\Block\ProductList\Attribute\Yesno" title="Editor's choice" attribute_code="recommended" products_count="1" column_count="1" order="default" dir="asc" template="Swissup_Highlight::product/list.phtml" mode="grid"}}</div>
            <div class="col-md-4 col-sm-4 col-xs-12">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="video_of_the_day"}}</div>
        </div>
    </div>
    <div class="col-lg-9 col-md-12">
        {{widget type="Swissup\Highlight\Block\ProductList\Featured" title="Featured Products" products_count="6" column_count="3" order="default" dir="asc" template="Swissup_Highlight::product/list.phtml" mode="grid"}}
        <div class="new-products-slider">
            {{widget type="Swissup\Highlight\Block\ProductList\NewList" title="New Products" carousel="1" products_count="4" column_count="4" page_count="5" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="0" hide_when_filter_is_used="0"}}
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
                'identifier' => 'argento_mall',
                'title'      => 'Argento Mall',
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
                        'image' => 'argento/mall/argento_mall_slide_1.jpg',
                        'title' => 'Slide 1',
                        'description' => 'Sony VAIO Laptop',
                        'desc_position' => 'left',
                        'desc_background' => 'dark',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/mall/argento_mall_slide_2.jpg',
                        'title' => 'Slide 2',
                        'description' => 'Dell Studio 17',
                        'desc_position' => 'left',
                        'desc_background' => 'dark',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/mall/argento_mall_slide_3.jpg',
                        'title' => 'Slide 3',
                        'description' => 'HP HDX 16t',
                        'desc_position' => 'left',
                        'desc_background' => 'dark',
                        'sort_order' => 30
                    ],
                    [
                        'image' => 'argento/mall/argento_mall_slide_4.jpg',
                        'title' => 'Slide 4',
                        'description' => 'Nikon 5000',
                        'desc_position' => 'left',
                        'desc_background' => 'dark',
                        'sort_order' => 40
                    ],
                    [
                        'image' => 'argento/mall/argento_mall_slide_5.jpg',
                        'title' => 'Slide 5',
                        'description' => 'Apple Macbook',
                        'desc_position' => 'left',
                        'desc_background' => 'dark',
                        'sort_order' => 50
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
                'name' => 'argento_mall_home_top',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'free-shipping',
                        'title'      => 'Free Shipping',
                        'url'        => 'free-shipping',
                        'image'      => '/argento/mall/argento_mall_callout_home_top1.gif',
                        'width'      => 225,
                        'height'     => 130,
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
                'attribute_code' => 'featured',
                'frontend_label' => ['Featured'],
                'default_value'  => 0
            ],
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
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_AMAZON_TOP,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-mall'),
                'settings' => [
                    'max_depth' => 0,
                    'css_class' => 'navpro-amazon navpro-nowrap',
                    'identifier' => 'argento_mall_topmenu',
                ],
            ],
        ];
    }
}
