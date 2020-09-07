<?php
namespace Swissup\ThemeFrontendArgentoStripes\Upgrades;

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
            'Products' => [],
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
        $storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeIds = $this->getStoreIds();
        $storeToInstall = $storeManager->getStore(reset($storeIds));

        return [
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-stripes'),
            'cms/wysiwyg/enabled' => 'hidden',
            'catalog/frontend/grid_per_page_values' => '12,20,32',
            'catalog/frontend/grid_per_page' => '20',
            'catalog/frontend/list_per_page_values' => '5,10,15,20,25',
            'catalog/frontend/list_per_page' => '5',

            'fblike/product/enabled' => 1,
            'hovergallery/general/enabled' => 1,
            'fblike/product/layout' => 'custom',
            'featuredattributes/general/enabled' => 1,
            'lightboxpro/general/thumbnails' => 'vertical',
            'lightboxpro/size/thumbnail_width' => 70,
            'lightboxpro/size/thumbnail_height' => 80,
            'lightboxpro/size/thumbnail_margin' => 10,
            'lightboxpro/popup/type' => 'advanced',
            'prolabels/general/base' => '.fotorama__stage__frame.fotorama__active .fotorama__img',
            'prolabels/on_sale/product/active' => 1,
            'prolabels/on_sale/product/custom' => 'color: #fff; width: 60px; height: 30px;background:#FF6601; border-radius:2px;font-size: 14px;font-weight: bold;text-transform: uppercase; margin: 10px;',
            'prolabels/on_sale/category/active' => 1,
            'prolabels/on_sale/category/custom' => 'color: #fff; width: 40px; height: 20px;background:#FF6601; border-radius:2px;font-size: 12px;font-weight: bold;text-transform: uppercase;',
            'prolabels/is_new/product/active' => 1,
            'prolabels/is_new/product/custom' => 'color: #fff; width: 60px; height: 30px;background:#F54336; border-radius:2px;font-size: 14px;font-weight: bold;text-transform: uppercase; margin: 10px;',
            'prolabels/is_new/category/active' => 1,
            'prolabels/is_new/category/custom' => 'color: #fff; width: 40px; height: 20px;background:#F54336; border-radius:2px;font-size: 12px;font-weight: bold;text-transform: uppercase;',
            'reviewreminder/general/enabled' => 1,
            'soldtogether/order/enabled' => 1,
            'soldtogether/order/layout' => 'amazon-stripe',
            'soldtogether/order/count' => 4,
            'soldtogether/customer/enabled' => 1,
            'soldtogether/customer/count' => 10,
            'swissup_easytabs/product_tabs/layout' => 'expanded',
            'ajaxsearch/category/filter' => 1,
            // SEO Suite settings
            'swissup_seopager/general/enabled' => 1,
            'swissup_seopager/general/strategy' => 2,
            'swissup_seopager/general/strategy_no_view_all' => 2,
            'richsnippets/general/enabled' => 1,
            'richsnippets/website/siteurl' => $storeToInstall->getBaseUrl(),
            'swissup_seourls/general/enabled' => 1,
            'swissup_seourls/search/term_place' => 0,
            'swissup_seourls/layered_navigation/separate_filters' => 0,
            'swissup_seourls/cms/redirect_to_home' => 1
        ];
    }

    public function getCmsBlocks()
    {
        return [
            'footer_cms_content' => [
                'title' => 'Footer Cms Content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="footer-links">
    <ul class="footer links argento-grid">
        <li class="col-md-3 col-xs-12">
            <div data-role="title" class="h4">Get help</div>
            <ul data-role="content" class="links">
                <li><a href='{{store direct_url="faq"}}'>FAQ</a></li>
                <li><a href='{{store direct_url="forum"}}'>Forum</a></li>
                <li><a href='{{store direct_url="sales/guest/form/"}}'>Returns and Exchanges</a></li>
            </ul>
        </li>
        <li class="col-md-3 col-xs-12">
            <div data-role="title" class="h4">Learn more</div>
            <ul data-role="content" class="links">
                <li><a href='{{store direct_url="terms-and-conditions"}}'>Terms & Conditions</a></li>
                <li><a href='{{store direct_url="privacy-policy-cookie-restriction-mode"}}'>Privacy Policy</a></li>
                <li><a href='{{store direct_url="affiliate"}}'>Affiliate program</a></li>
            </ul>
        </li>
        <li class="col-md-3 col-xs-12">
            <div data-role="title" class="h4">Be inspired by</div>
            <ul data-role="content" class="links">
                <li><a href='{{store direct_url="blog"}}'>Blog</a></li>
                <li><a href='{{store direct_url="typography"}}'>Typography page</a></li>
                <li><a href='{{store direct_url="testimonials"}}'>Testimonials</a></li>
            </ul>
        </li>
        <li class="col-md-3 col-xs-12">
            <div data-role="title" class="h4">Get in touch</div>
            <ul data-role="content" class="links">
                <li><a href='{{store direct_url="contact#contact-links"}}'>Social media</a></li>
                <li><a href='{{store direct_url="contact"}}'>Contact Us</a></li>
                <li><a href='{{store direct_url="contact#map"}}'>Map</a></li>
            </ul>
        </li>
    </ul>
</div>
HTML
            ],
            'footer_payments' => [
                'title' => 'Footer Payments',
                'identifier' => 'footer_payments',
                'is_active' => 1,
                'content' => <<<HTML
<div class="footer-payments a-center">
    <img width="625" height="35"
        src="{{view url='images/payments.png'}}"
        srcset="{{view url='images/payments.png'}} 1x, {{view url='images/payments@2x.png'}} 2x"
        alt="Credit cards, we accept"
    />
</div>
HTML
            ],
            'footer_contacts' => [
                'title' => 'Footer Contacts',
                'identifier' => 'footer_contacts',
                'is_active' => 1,
                'content' => <<<HTML
<div class="footer-contacts argento-grid">
    <div class="col-md-3 hidden-xs">
        <img width="192" height="241"
            src="{{view url='images/support.png'}}"
            srcset="{{view url='images/support.png'}} 1x, {{view url='images/support@2x.png'}} 2x"
            alt="need help?"
        />
    </div>
    <div class="col-md-6 col-xs-12">
        <div data-role="title" class="h4">Need Help?</div>
        <div class="argento-grid">
            <div class="col-lg-6 col-md-12 block">
                <div class="block-icon">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-map-marker fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <div class="block-content">
                    <div data-role="title">Address</div>
                    <div data-role="content">
                        <div>FifthAve Street 31</div>
                        <div>New York</div>
                        <div>USA</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="block">
                    <div class="block-icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-envelope fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <div class="block-content">
                        <div data-role="title">Email</div>
                        <div data-role="content">contact@company.com</div>
                    </div>
                </div>
                <div class="block">
                    <div class="block-icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-mobile fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <div class="block-content">
                        <div data-role="title">Phone</div>
                        <div data-role="content">+01 122 334 566</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-xs-12">
        <div data-role="title" class="h4">Connect With Us</div>
        <div class="social-icons colorize-fa-stack-hover">
            <a href="https://facebook.com/" class="icon icon-facebook">
                <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a href="https://youtube.com/" class="icon icon-youtube">
                <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-youtube-play fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a href="https://twitter.com/" class="icon icon-twitter">
                <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                </span>
            </a>
            <a href="https://www.instagram.com/" class="icon icon-instagram">
                <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-instagram fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        </div>
    </div>
</div>
HTML
            ],
            'contact_aside_cms_content' => [
                'title' => 'Contact Aside Cms Content',
                'identifier' => 'contact_aside_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<article>
    <h2>Got Questions?</h2>
    <p>If you’d like to learn more about us and our products, get in touch!</p>
    <div class="argento-grid" id="contact-links">
        <div class="col-xs-12 col-sm-6">
            <h5>Company Info</h5>
            <p>STRIPES COMMERCE <br />FifthAve Street 31 <br />New York, USA</p>
        </div>
        <div class="col-xs-12 col-sm-6">
            <h5>Contact Info</h5>
            <p>contact@company.com <br /> +01 122 334 566</p>
        </div>
    </div>
    <h5>Follow Us</h5>
    <div class="social-icons colorize-fa-stack-hover red-on-gray">
        <a href="https://facebook.com/" class="icon icon-facebook">
            <span class="fa-stack fa-2x">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="https://youtube.com/" class="icon icon-youtube">
            <span class="fa-stack fa-2x">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-youtube-play fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="https://twitter.com/" class="icon icon-twitter">
            <span class="fa-stack fa-2x">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
            </span>
        </a>
        <a href="https://www.instagram.com/" class="icon icon-instagram">
            <span class="fa-stack fa-2x">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-instagram fa-stack-1x fa-inverse"></i>
            </span>
        </a>
    </div>
</article>
HTML
            ],
            'contact_footer_map' => [
                'title' => 'Contact Footer Map',
                'identifier' => 'contact_footer_map',
                'is_active' => 1,
                'content' => <<<HTML
<div id="map" class="contact-map-placeholder" style='background-image: url({{media url="wysiwyg/contact-map.png"}})'>
</div>
HTML
            ]
        ];
    }

    public function getCmsPages()
    {
        return [
            'home' => [
                'title' => 'Argento Stripes',
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
    {{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_stripes"}}
</div>

{{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_stripes_home_top"}}

{{widget type="Swissup\Highlight\Block\ProductList\All" title="Top Selling<br><span>Headphones</span>" carousel="1" products_count="8" column_count="4" page_count="1" order="default" dir="asc" template="Magento_Catalog::product/list.phtml" mode="grid" hide_when_filter_is_used="0" css_class="hl-magazine hl-blue hl-contain" title_image_url="highlight/argento/stripes/headphones.png" show_page_link="1" page_url="highlight/bestsellers.html" page_link_position="top" page_link_title="View All"}}

{{widget type="Swissup\Highlight\Block\ProductList\All" title="Top Selling<br><span>Smartphones</span>" carousel="1" products_count="8" column_count="4" page_count="1" order="default" dir="asc" template="Magento_Catalog::product/list.phtml" mode="grid" hide_when_filter_is_used="0" css_class="hl-magazine hl-orange hl-contain" title_image_url="highlight/argento/stripes/smartphones.png" show_page_link="1" page_url="highlight/bestsellers.html" page_link_position="top" page_link_title="View All"}}

{{widget type="Swissup\Highlight\Block\ProductList\All" title="Top Selling<br><span>Activity<br>Trackers</span>" carousel="1" products_count="8" column_count="4" page_count="1" order="default" dir="asc" template="Magento_Catalog::product/list.phtml" mode="grid" hide_when_filter_is_used="0" css_class="hl-magazine hl-green hl-contain" title_image_url="highlight/argento/stripes/activity.png" show_page_link="1" page_url="highlight/bestsellers.html" page_link_position="top" page_link_title="View All"}}

{{widget type="Swissup\Highlight\Block\ProductList\All" title="Top Selling<br><span>Smart TVs</span>" carousel="1" products_count="8" column_count="4" page_count="1" order="default" dir="asc" template="Magento_Catalog::product/list.phtml" mode="grid" hide_when_filter_is_used="0" css_class="hl-magazine hl-purple hl-contain" title_image_url="highlight/argento/stripes/smart-tv.png" show_page_link="1" page_url="highlight/bestsellers.html" page_link_position="top" page_link_title="View All"}}

{{widget type="Swissup\Highlight\Block\ProductList\All" title="Top Selling<br><span>Home<br>Electronics</span>" carousel="1" products_count="8" column_count="4" page_count="1" order="default" dir="asc" template="Magento_Catalog::product/list.phtml" mode="grid" hide_when_filter_is_used="0" css_class="hl-magazine hl-red hl-contain" title_image_url="highlight/argento/stripes/electronics.png" show_page_link="1" page_url="highlight/bestsellers.html" page_link_position="top" page_link_title="View All"}}

{{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_stripes_home_bottom"}}

<div class="block row widget block-promo block-carousel">
    <div class="block-content">
        <div class="slick-slider" data-mage-init='{"slick": {"slidesToShow": 7, "slidesToScroll": 1, "dots": false, "autoplay": true, "variableWidth": true, "swipeToSlide": true, "rows": 0, "lazyLoad": "ondemand"}}'>
            <div><a href="#"><img data-lazy="{{view url='images/brands/sony.jpg'}}" alt="" width="128" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/apple.png'}}" alt="" width="64" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/garmin.jpg'}}" alt="" width="154" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/htc.jpg'}}" alt="" width="124" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/intel.jpg'}}" alt="" width="103" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/motorola.jpg'}}" alt="" width="204" height="76"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/nike.png'}}" alt="" width="118" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/samsung.png'}}" alt="" width="128" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/sony.jpg'}}" alt="" width="128" height="73"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/apple.png'}}" alt="" width="64" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/garmin.jpg'}}" alt="" width="154" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/htc.jpg'}}" alt="" width="124" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/intel.jpg'}}" alt="" width="103" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/motorola.jpg'}}" alt="" width="204" height="76"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/nike.png'}}" alt="" width="118" height="74"/></a></div>
            <div><a href="#"><img data-lazy="{{view url='images/brands/samsung.png'}}" alt="" width="128" height="74"/></a></div>
        </div>
    </div>
</div>
HTML
            ],
            'typography' => [
                'title' => 'Typography',
                'identifier' => 'typography',
                'page_layout' => '1column',
                'content_heading' => '',
                'is_active' => 1,
                'layout_update_xml' => '',
                'custom_theme' => null,
                'custom_root_template' => null,
                'custom_layout_update_xml' => null,
                'content' => <<<HTML
<section id="headings" > <!-- big headings -->
    <div class="argento-grid">
        <div class="col-md-12">
            <div class="hero">
                <div class="page-title-wrapper">
                    <h1 class="page-title">H1. Responsive Magento template with extensive functionality</h1>
                </div>
                <p class="subtitle a-center">Argento gives your online business countless possibilities. Theme comes with 6 awesome designs and 20 feature-rich modules.
                </p>
            </div>
        </div>
    </div>
</section>
<section> <!-- h1 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h1>H1. 6 stunning designs that convert</h1>
                <p>
                    Our template offers Luxury, Argento, Flat, Mall, Pure and Pure 2.0 themes to design a magnificent presentation of your store. You can also choose your favorite layout from 3 layout types: standard, boxed and fullwidth.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-letter"></div>
                </div>
                <div class="media-body">
                    <h1>H1. Make the products look impressive</h1>
                    <p>
                        <a rel="nofollow" href="https://swissuplabs.com/magento-lightbox-extension.html"
                        title="Magento module">Lightbox Pro</a> extension adds the lightbox popup anywhere on site. Using <a rel="nofollow" href="https://swissuplabs.com/magento-slider-extension.html" title="Easyslider">Image Slider</a> and <a rel="nofollow" href="https://swissuplabs.com/magento-slider-extension.html" title="Magento module">Slick Carousel</a> modules you will easily create nice sliders. <a rel="nofollow" href="https://swissuplabs.com/magento-product-labels-extension.html" title="Magento module">ProLabels</a> module helps you add custom product labels as well as add ready to use labels for New, On Sale, In/Out of stock items.
                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section> <!-- h2 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h2>H2. Designed with best SEO practices</h2>
                <p>
                    Make your site ranking benefit from Argento. The template comes with SEO Suite module that includes Rich Snippets, <a rel="nofollow" href="https://swissuplabs.com/magento-lightbox-extension.html" title="Magento module">HTML</a> and <a rel="nofollow" href="http://docs.swissuplabs.com/m1/extensions/seo-xml-sitemap/" title="Magento module">XML</a>, SEO metadata templates. Use Argento to deliver highly relevant search results quickly in search engines like Yahoo, Google, Bing, etc.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-bulb"></div>
                </div>
                <div class="media-body">
                    <h2>H2. Help arrange products perfectly</h2>
                    <p>
                        <a rel="nofollow" href="https://swissuplabs.com/magento-custom-product-list-extension.html" title="Magento module">Highlight</a> module shows New, Featured, Onsale, Bestsellers, Popular product lists with filters. <a rel="nofollow" href="https://swissuplabs.com/easy-catalog-images.html" title="Magento module">Easy Catalog Images</a> adds category/subcategory listing block with assigned images everywhere. <a rel="nofollow" href="https://swissuplabs.com/magento-attributes-and-brands-pages.html" title="Magento module">Attribute/Brand pages</a> creates brands pages, menu with brands. <a rel="nofollow" href="https://swissuplabs.com/product-tabs-magento-extension.html" title="Magento module">Easy Tabs</a> shows a product page content in attractive product tabs.
                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section> <!-- h3 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h3>H3. Highly customizable, easy to style</h3>
                <p>
                    Argento is very flexible. It allows you create custom themes and subthemes without modification of core theme files. Using the override feature, you can easily change css styles, the template and layout files. Via backend configurator you can change color scheme, font, header, etc.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-compass"></div>
                </div>
                <div class="media-body">
                    <h3>H3. Bring excellent user experience</h3>
                    <p>
                        <a rel="nofollow" href="https://swissuplabs.com/easy-flags.html" title="Magento module">Easy Flags</a> module comes with nice flag buttons instead of plain store switcher. <a rel="nofollow" href="https://swissuplabs.com/facebook-like-button.html" title="Magento module">Facebook Like button</a> helps users spread a store content. <a rel="nofollow" href="https://swissuplabs.com/magento-products-questions-extension.html" title="Magento module">Ask It</a> extension adds the products questions block on product, category page and CMS pages. <a rel="nofollow" href="https://swissuplabs.com/magento-ajax-extension.html" title="Magento module">Ajax Pro</a> module enables ajax functionality all over.
                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section> <!-- h4 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h4>H4. Works great on mobile devices</h4>
                <p>
                    Argento template was created with mobile web design practices. No need of mobile template. Mobile-friendly theme works perfectly on iOS, Android and BlackBerry. Due to responsive design and built-in AMP support, your site will look excellent on any device.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-alert"></div>
                </div>
                <div class="media-body">
                    <h4>H4. Impact user experience in search</h4>
                    <p>
                        <a rel="nofollow" href="https://swissuplabs.com/magento-amp-extension.html" title="Magento module">AMP</a> module makes your site highly visible in Google search for mobile visitors. <a rel="nofollow" href="https://swissuplabs.com/magento-ajax-search-and-autocomplete-extension.html" title="Magento module">Ajax Search</a> adds the search by product description, keywords, CMS pages and catalog categories. <a rel="nofollow" href="https://swissuplabs.com/magento-seo-extension-rich-snippets.html" title="Magento module">Rich Snippets</a> help users see the information about your site. <a rel="nofollow" href="https://swissuplabs.com/magento-navigation-pro-extension.html" title="Magento module">Navigation Pro</a> creates fantastic menu with custom items and dropdown content based on categories of your store.
                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section> <!-- h3 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h5>H5. Fastest loading responsive theme</h5>
                <p>
                    Based on CSS sprite techniques, Argento reduces a number of https requests. In order to boost download speed, CSS and JSS files are based on clean code that can be minified by default Magento merger and other popular modules such as Fooman Speedster or GT Page Speed.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-compass"></div>
                </div>
                <div class="media-body">
                    <h5>H5. Help increase your revenue</h5>
                    <p>
                        <a rel="nofollow" href="https://swissuplabs.com/magento-sold-together-extension.html" title="Magento module">Sold Together</a> module blocks help you show more complementary products. <a rel="nofollow" href="https://swissuplabs.com/magento-banners-and-custom-blocks-extension.html" title="Magento module">Easy Banners</a> directs specific products at specific customers groups via placing banners or any other custom content. <a rel="nofollow" href="https://swissuplabs.com/magento-review-reminder-extension.html" title="Magento module">Review Reminder</a> aims to increase the number of reviews on your web pages. Via <a rel="nofollow" href="https://swissuplabs.com/testimonials.html" title="Magento module">Testimonials</a> module you can place testimonials listing anywhere using widgets.
                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section> <!-- h4 -->
    <div class="argento-grid">
        <div class="col-md-5">
            <article>
                <h6>H6. Magento 2 theme for Community edition</h6>
                <p>
                    Argento is available for Magento 2. Five beautiful designs such as Blank, Essence, Flat, Pure2 and Mall will prettify your Magento 2 ecommerce store. Besides you get 18 Magento modules included in package.
                </p>
            </article>
        </div>
        <div class="col-md-6 col-md-push-1">
            <article class="media">
                <div class="media-left">
                    <div class="media-object luxury-icon luxury-alert"></div>
                </div>
                <div class="media-body">
                    <h6>H6. Designed for any kind of store</h6>
                    <p>
                        Whatever site you run, use Argento theme. This is a template with unique designs and elegant layouts. Being modern and multipurpose, it will be suitable for fashion store, jewelry, toys, bags, watches, computer, etc.

                    </p>
                    <a rel="nofollow" href="#" class="read-more">Read more</a>
                </div>
            </article>
        </div>
    </div>
</section>
<section id="highlights" class="typography-3-columns" > <!-- 3-col-blocks -->
    <div class="argento-grid">
        <div class="col-md-4">
            <article class="a-center">
                <div class="luxury-icon luxury-cart-alt"></div>
                <h4>H4. Ajax powered</h4>
                <p>
                    With Argento theme you get fully AJAX driven e-commerce store. Ajax search autocomplete feature, ajax login popup, ajax shopping cart, ajax add to cart/ wishlist/ compare options are available.
                </p>
            </article>
        </div>
        <div class="col-md-4">
            <article class="a-center">
                <div class="luxury-icon luxury-lock"></div>
                <h4>H4. Magento Community / Enterprise</h4>
                <p>
                    Argento includes features of Magento Enterprise edition. Our template works with Magento EE 1.11.x-1.14.x, Magento CE 1.6.x-1.9.x. The template is also compatible with Magento CE 2.0.x-2.1.x.
                </p>
            </article>
        </div>
        <div class="col-md-4">
            <article class="a-center">
                <div class="luxury-icon luxury-headphones"></div>
                <h4>H4. Fast theme updates </h4>
                <p>
                    New Argento features are released every month to meet growing demands of our customers. Flexible theme structure supports adding enhancements and fast timely updates. Vote for new features.
                </p>
            </article>
        </div>
    </div>
</section>
<section id="lists"> <!-- lists -->
    <div class="argento-grid">
        <div class="col-md-3">
            <h3>H3. The fastest in all</h3>
            <ol>
                <li>quick to install</li>
                <li>enhanced theme editor</li>
                <li>reduced inline JavaScript</li>
                <li>resized homepage images</li>
            </ol>
        </div>
        <div class="col-md-3">
            <h3>H3. Mobile ready</h3>
            <ul>
                <li>floating navigation bar</li>
                <li>crisp logo for mobile</li>
                <li>modern styles of web forms</li>
                <li>mobile Swiper touch slider</li>
            </ul>
        </div>
        <div class="col-md-3">
            <h3>H3. Good usability</h3>
            <ul>
                <li class="icon icon-leaf">Amazon style menu</li>
                <li class="icon icon-pencil">sticky header and sidebar</li>
                <li class="icon icon-heart">product image hover feature</li>
                <li class="icon icon-lens">bootstrap support</li>
            </ul>
        </div>
        <div class="col-md-3">
            <h3>H3. Extended possibilities</h3>
            <ul class="circles">
                <li>changeable radio button color </li>
                <li>fancy stars in Review forms</li>
                <li>advanced layout settings</li>
                <li>unlimited product carousels</li>
            </ul>
        </div>
    </div>
</section>
<section id="tables">
    <div class="argento-grid">
        <div class="col-md-12">
            <table class="table table-striped data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<section id="messages"> <!-- system messages -->
    <div class="argento-grid">
        <div class="col-md-12">
            <h2>Important things in Argento</h2>
            <div class="message success">
                <div>Improved design of main UI elements.</div>
            </div>
            <div class="message error">
                <div>50+ configurable options</div>
            </div>
            <div class="message warning">
                <div>Magento 2 available.</div>
            </div>
            <div class="message info">
                <div>20 Magento modules included</div>
            </div>
        </div>
    </div>
</section>
HTML
            ]
        ];
    }

    public function getEasyslide()
    {
        return [
            [
                'identifier' => 'argento_stripes',
                'title'      => 'Argento Stripes',
                'direction' => 'horizontal',
                'speed' => 1000,
                'pagination' => '1',
                'navigation' => '1',
                'scrollbar' => '0',
                'autoplay' => 5000,
                'effect' => 'slide',
                'theme' => 'black',
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
                        'image' => 'argento/stripes/argento_stripes_slider1.png',
                        'title' => 'New Gear at Unbelievable Prices!',
                        'link' => 'home-and-fitness.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/stripes/argento_stripes_slider2.png',
                        'title' => 'New Gear at Unbelievable Prices!',
                        'link' => 'home-and-fitness.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/stripes/argento_stripes_slider3.png',
                        'title' => 'New Gear at Unbelievable Prices!',
                        'link' => 'home-and-fitness.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 30
                    ],
                    [
                        'image' => 'argento/stripes/argento_stripes_slider4.png',
                        'title' => 'New Gear at Unbelievable Prices!',
                        'link' => 'home-and-fitness.html',
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 40
                    ]
                ]
            ]
        ];
    }

    public function getEasytabs()
    {
        $this->unsetEasytab(
            'Magento\Catalog\Block\Product\View\Attributes',
            $this->getStoreIds()
        );
        return [
            [
                'title' => 'Questions ({{eval code="getCount()"}})',
                'alias' => 'questions',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 18,
                'status' => 1,
                'widget_tab' => 0,
                'widget_template' => 'template.phtml',
                'widget_unset' => 'askit_listing,askit_form',
                'widget_block' => 'Swissup\Askit\Block\Question\Widget'
            ]
        ];
    }

    public function getEasybanner()
    {
        return [
            [
                'name' => 'argento_stripes_content_top',
                'limit' => 1,
                'additional_css_class' => 'hidden-xs',
                'container' => 'top.container',
                'position' => 'before="-"',
                'banners' => [
                    [
                        'identifier' => 'argento-stripes-benefits',
                        'title'      => 'Benefits',
                        'url'        => 'benefits',
                        'mode'       => 'html',
                        'html'       => <<<HTML
<div>
    <div class="icon"><i class="fa fa-2x fa-truck"></i></div>
    <div class="content">
        <div class="title">Free shipping</div>
        <div class="text">Free for all orders over $50</div>
    </div>
</div>
<div class="separator"></div>
<div>
    <div class="icon"><i class="fa fa-2x fa-clock-o"></i></div>
    <div class="content">
        <div class="title">24/7 support</div>
        <div class="text">Fast, professional support</div>
    </div>
</div>
<div class="separator"></div>
<div>
    <div class="icon"><i class="fa fa-2x fa-calendar"></i></div>
    <div class="content">
        <div class="title">Extended return period</div>
        <div class="text">30 days return policy</div>
    </div>
</div>
<div class="separator"></div>
<div>
    <div class="icon"><i class="fa fa-2x fa-calendar-check-o"></i></div>
    <div class="content">
        <div class="title">Choose delivery day</div>
        <div class="text">Flexible delivery dates</div>
    </div>
</div>
HTML
                    ],
                    [
                        'identifier' => 'argento-stripes-newsletter',
                        'title'      => 'Newletter Popup',
                        'url'        => 'newletter',
                        'type'       => \Swissup\Easybanner\Model\Banner::TYPE_LIGHTBOX,
                        'mode'       => 'html',
                        'class_name' => 'no-border permanent-close-hide',
                        'conditions_serialized' => json_encode([
                            'type' => \Swissup\Easybanner\Model\Rule\Condition\Combine::class,
                            'attribute' => null,
                            'operator' => null,
                            'value' => '1',
                            'is_value_processed' => null,
                            'aggregator' => 'all',
                            'conditions' => [
                                [
                                    'type' => \Swissup\Easybanner\Model\Rule\Condition\Banner::class,
                                    'attribute' => 'activity_time',
                                    'operator' => '>',
                                    'value' => '20',
                                    'is_value_processed' => false,
                                ],
                                [
                                    'type' => \Swissup\Easybanner\Model\Rule\Condition\Banner::class,
                                    'attribute' => 'display_count_per_customer_per_day',
                                    'operator' => '<',
                                    'value' => '1',
                                    'is_value_processed' => false,
                                ],
                            ],
                        ]),
                        'html'       => <<<HTML
<div class="easybanner-layout-book">
    <img src="{{media url='easybanner/argento/stripes/newsletter-popup.png'}}" alt="" />
    <div class="content center">
        <h2 class="easybanner-title"><strong>Don't miss out!</strong></h2>
        <p>
            Sign up for our newsletter to get the latest news about our company,
            your favorite products, and great deals.
        </p>
        <div class="easybanner-newsletter">
            {{block class="Magento\Newsletter\Block\Subscribe" name="easybanner.newsletter-subscribe" template="Magento_Newsletter::subscribe.phtml"}}
        </div>
    </div>
</div>
HTML
                    ]
                ]
            ],
            [
                'name' => 'argento_stripes_home_top',
                'limit' => 2,
                'banners' => [
                    [
                        'identifier' => 'new-products',
                        'title'      => 'New Products',
                        'url'        => 'highlight/new.html',
                        'image'      => '/argento/stripes/home-top-banner-1.png',
                        'width'      => 0,
                        'height'     => 0,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                    [
                        'identifier' => 'special-offers',
                        'title'      => 'Special Offers',
                        'url'        => 'highlight/onsale.html',
                        'image'      => '/argento/stripes/home-top-banner-2.png',
                        'width'      => 0,
                        'height'     => 0,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ]
                ]
            ],
            [
                'name' => 'argento_stripes_home_bottom',
                'limit' => 3,
                'banners' => [
                    [
                        'identifier' => 'new-collection',
                        'title'      => 'New Collection',
                        'url'        => 'fitbit-activity-2.html',
                        'image'      => '/argento/stripes/home-bottom-banner-1.png',
                        'width'      => 0,
                        'height'     => 0,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                    [
                        'identifier' => 'spring-clearance',
                        'title'      => 'Spring Clearance',
                        'url'        => 'nest-thermostat.html',
                        'image'      => '/argento/stripes/home-bottom-banner-2.png',
                        'width'      => 0,
                        'height'     => 0,
                        'resize_image' => 0,
                        'retina_support' => 0
                    ],
                    [
                        'identifier' => 'connected-home',
                        'title'      => 'Connected Home',
                        'url'        => 'home-and-fitness.html',
                        'image'      => '/argento/stripes/home-bottom-banner-3.png',
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
                'type' => \Swissup\Navigationpro\Model\Config\Source\BuilderType::TYPE_AMAZON_TOP,
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-stripes'),
                'settings' => [
                    'max_depth' => 0,
                    'identifier' => 'argento_stripes_topmenu',
                ],
                'items' => [
                    'departments' => [
                        'css_class' => 'navpro-departments navpro-overlay',
                    ],
                ],
            ],
        ];
    }
}
