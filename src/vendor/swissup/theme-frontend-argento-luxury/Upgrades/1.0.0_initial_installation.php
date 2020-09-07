<?php
namespace Swissup\ThemeFrontendArgentoLuxury\Upgrades;

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
            'design/theme/theme_id' => $this->getThemeId('frontend/Swissup/argento-luxury'),
            'cms/wysiwyg/enabled' => 'hidden',
            'ajaxsearch/folded/enable' => 1,
            'ajaxsearch/folded/effect' => 'slide-down',
            'fblike/product/enabled' => 1,
            'fblike/product/layout' => 'custom',
            'hovergallery/general/enabled' => 1,
            'lightboxpro/popup/type' => 'advanced',
            'prolabels/general/base' => '.fotorama__stage__frame:first-child',
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
            'footer_cms_content' => [
                'title' => 'footer_cms_content',
                'identifier' => 'footer_cms_content',
                'is_active' => 1,
                'content' => <<<HTML
<div class="argento-grid">
    <div class="col-md-9">
        <ul class="footer links argento-grid">
            <li class="col-md-4 col-xs-12">
                <div data-role="title" class="h4">About us</div>
                <ul data-role="content" class="links">
                    <li><a href='{{store direct_url="about"}}'>About Us</a></li>
                    <li><a href='{{store direct_url="our-company"}}'>Our company</a></li>
                    <li><a href='{{store direct_url="carriers"}}'>Carriers</a></li>
                    <li><a href='{{store direct_url="shipping"}}'>Shipping</a></li>
                </ul>
            </li>
            <li class="col-md-4 col-xs-12">
                <div data-role="title" class="h4">Customer center</div>
                <ul data-role="content" class="links">
                    <li><a href='{{store direct_url="customer/account"}}'>My Account</a></li>
                    <li><a href='{{store direct_url="sales/order/history"}}'>Order Status</a></li>
                    <li><a href='{{store direct_url="wishlist"}}'>Wishlist</a></li>
                    <li><a href='{{store direct_url="exchanges"}}'>Returns and Exchanges</a></li>
                </ul>
            </li>
            <li class="col-md-4 col-xs-12">
                <div data-role="title" class="h4">Info</div>
                <ul data-role="content" class="links">
                    <li><a href='{{store direct_url="typography"}}'>Typography page</a></li>
                    <li><a href='{{store direct_url="sitemap"}}'>Site Map</a></li>
                    <li><a href='{{store direct_url="delivery"}}'>Delivery information</a></li>
                    <li><a href='{{store direct_url="returns"}}'>Returns policy</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="col-md-3 footer-contacts">
        <div class="h4">Call Us</div>
        <span>
            We're available 24/7<br/>
            <strong>1.800.555.1903</strong><br/>
        </span>
        <div>Secure payments by</div>
        <img width="220" height="25" style="margin-top: 10px;" src="{{view url='images/payments.png'}}"" srcset="{{view url='images/payments@2x.png'}} 2x" alt="Security Seal"/>
    </div>
</div>
<div class="social-icons colorize-fa-hover">
    <a href="http://twitter.com"><i class="fa fa-2x fa-twitter"></i></a>
    <a href="facebook.com"><i class="fa fa-2x fa-facebook"></i></a>
    <a href="youtube.com"><i class="fa fa-2x fa-youtube"></i></a>
    <a href="rss.com"><i class="fa fa-2x fa-rss"></i></a>
</div>
HTML
            ],
            'product_page_additional_tabs' => [
                'title' => 'product_page_additional_tabs',
                'identifier' => 'product_page_additional_tabs',
                'is_active' => 1,
                'content' => <<<HTML
<div class="page-before-footer cms-container">
    <div class="content">
        {{widget type="Swissup\Easytabs\Block\WidgetTabs" filter_tabs="customers_buy_tabbed, askit_tabbed, viewed_tabbed" template="Swissup_Easytabs::tabs.phtml"}}
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
                'title' => 'Argento Luxury',
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
    {{widget type="Swissup\EasySlide\Block\Slider" identifier="argento_luxury"}}
</div>
<div class="jumbotron no-padding">
    <div class="container">
        <div class="row">
            {{widget type="Swissup\Easycatalogimg\Block\Widget\SubcategoriesList" category_count="5" column_count="5" show_image="1" image_width="382" image_height="565" resize_image="0" template="Swissup_Easycatalogimg::list.phtml"}}
        </div>
    </div>
</div>
<div class="jumbotron">
    <div class="container">
        <div class="row">
            {{widget type="Swissup\Highlight\Block\ProductList\NewList" title="New Arrivals" products_count="4" column_count="4" order="default" dir="desc" template="Swissup_Highlight::product/list.phtml" mode="grid" show_page_link="1" page_link_position="bottom" page_link_title="Shop Now"}}
        </div>
    </div>
</div>
<div class="row jumbotron">
    <div class="hero block-homepage-banner">
        {{widget type="Swissup\Easybanner\Block\Placeholder" placeholder="argento_luxury_home"}}
    </div>
</div>
<div class="jumbotron hero">
    <div class="container">
        <div class="block block-benefits">
            <div class="block-content argento-grid">
                <div class="col-md-4">
                    <div class="luxury-icon luxury-icon-big luxury-cart-alt"></div>
                    <h4>Free Delivery</h4>
                    <p>Our store delivers an extensive and expertly curated selection of fashion and lifestyle offerings.</p>
                </div>
                <div class="col-md-4">
                    <div class="luxury-icon luxury-icon-big luxury-lock"></div>
                    <h4>Secure Payment</h4>
                    <p>Our store delivers an extensive and expertly curated selection of fashion and lifestyle offerings.</p>
                </div>
                <div class="col-md-4">
                    <div class="luxury-icon luxury-icon-big luxury-headphones"></div>
                    <h4>24h Customer Service</h4>
                    <p>Our store delivers an extensive and expertly curated selection of fashion and lifestyle offerings.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="jumbotron jumbotron-slick">
    <div class="container">
        <div class="block widget block-carousel">
            <div class="block-content">
                <div class="slick-slider" data-mage-init='{"slick": {"slidesToShow": 8, "slidesToScroll": 1, "dots": false, "autoplay": true, "variableWidth": true, "swipeToSlide": true, "rows": 0, "lazyLoad": "ondemand"}}'>
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
            <div class="message success">IMPROVED DESIGN OF MAIN UI ELEMENTS.</div>
            <div class="message error">50+ CONFIGURABLE OPTIONS</div>
            <div class="message warning">MAGENTO 2 AVAILABLE.</div>
            <div class="message info">20 MAGENTO MODULES INCLUDED</div>
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
                'identifier' => 'argento_luxury',
                'title'      => 'Argento Luxury',
                'speed' => 1000,
                'pagination' => '1',
                'navigation' => '0',
                'scrollbar' => '0',
                'autoplay' => 5000,
                'effect' => 'slide',
                'theme' => 'black',
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
                        'image' => 'argento/luxury/argento_luxury_slider1.jpg',
                        'title' => 'Slide 1',
                        'description' => <<<HTML
<div>
    <h4>New Style</h4>
</div>
<div>
    <h1>Urban Summer</h1>
</div>
<div>
    <button class="button btn-alt"><span>Shop Now</span></button>
</div>
HTML
,
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 10
                    ],
                    [
                        'image' => 'argento/luxury/argento_luxury_slider2.jpg',
                        'title' => 'Slide 2',
                        'description' => <<<HTML
<div>
    <h4>New Style</h4>
</div>
<div>
    <h1>Urban Summer</h1>
</div>
<div>
    <button class="button btn-alt"><span>Shop Now</span></button>
</div>
HTML
,
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 20
                    ],
                    [
                        'image' => 'argento/luxury/argento_luxury_slider3.jpg',
                        'title' => 'Slide 3',
                        'description' => <<<HTML
<div>
    <h4>New Style</h4>
</div>
<div>
    <h1>Urban Summer</h1>
</div>
<div>
    <button class="button btn-alt"><span>Shop Now</span></button>
</div>
HTML
,
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 30
                    ],
                    [
                        'image' => 'argento/luxury/argento_luxury_slider4.jpg',
                        'title' => 'Slide 4',
                        'description' => <<<HTML
<div>
    <h4>New Style</h4>
</div>
<div>
    <h1>Urban Summer</h1>
</div>
<div>
    <button class="button btn-alt"><span>Shop Now</span></button>
</div>
HTML
,
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 40
                    ],
                    [
                        'image' => 'argento/luxury/argento_luxury_slider5.jpg',
                        'title' => 'Slide 5',
                        'description' => <<<HTML
<div>
    <h4>New Style</h4>
</div>
<div>
    <h1>Urban Summer</h1>
</div>
<div>
    <button class="button btn-alt"><span>Shop Now</span></button>
</div>
HTML
,
                        'desc_position' => 'top',
                        'desc_background' => 'transparent',
                        'sort_order' => 50
                    ]
                ]
            ]
        ];
    }

    public function getEasytabs()
    {
        // unset product tabs for storeviews with luxury except product description
        $unsetTabs = $this->objectManager
            ->create('Swissup\Easytabs\Model\Entity')
            ->getCollection()
            ->addProductTabFilter()
            ->addFieldToFilter(
                'block',
                [
                    'nin' => [
                        'Magento\Catalog\Block\Product\View\Description',
                        'Swissup\Easytabs\Block\Tab\Product\Review'
                    ]
                ]
            )
            ->addFieldToFilter('status', '1');
        foreach ($unsetTabs as $tab) {
            $this->unsetEasytab(null, $this->getStoreIds(), $tab->getAlias());
        }

        return array(
            array(
                'title' => 'Free delivery and returns',
                'alias' => 'delivery_and_returns_tabbed',
                'block' => 'Swissup\Easytabs\Block\Tab\Html',
                'sort_order' => 99,
                'status' => 1,
                'widget_tab' => 0,
                'widget_content' => "<p>Our store delivers an extensive and expertly curated selection of fashion and lifestyle offerings.</p>\n<p>If you are not satisfied with your order - send it back within 30 days after day of purchase!</p>"
            ),
            array(
                'title' => 'Customers also buy',
                'alias' => 'customers_buy_tabbed',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 10,
                'status' => 1,
                'widget_tab' => 1,
                'widget_block' => 'Swissup\SoldTogether\Block\Customer',
                'widget_template' => 'Swissup_SoldTogether::product/customer.phtml',
                'widget_unset' => 'soldtogether.product.customer'
            ),
            array(
                'title' => 'Questions',
                'alias' => 'askit_tabbed',
                'block' => 'Swissup\Easytabs\Block\Tab\Template',
                'sort_order' => 90,
                'status' => 1,
                'widget_tab' => 1,
                'widget_block' => 'Swissup\Askit\Block\Question\Widget',
                'widget_template' => 'none',
                'widget_unset' => null
            ),
            array(
                'title' => 'Recently viewed',
                'alias' => 'viewed_tabbed',
                'block' => 'Swissup\Easytabs\Block\Tab\Html',
                'sort_order' => 99,
                'status' => 1,
                'widget_tab' => 1,
                'widget_content' => '{{widget type="Magento\Reports\Block\Product\Widget\Viewed" page_size="5" template="widget/viewed/content/viewed_grid.phtml"}}'
            )
        );
    }

    public function getEasybanner()
    {
        return [
            [
                'name' => 'argento_luxury_home',
                'limit' => 1,
                'banners' => [
                    [
                        'identifier' => 'argento-luxury-home1',
                        'title'      => 'Special Offer',
                        'url'        => 'free-shipping',
                        'image'      => '/argento/luxury/argento_luxury_callout_home1.png',
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
                'theme_id' => $this->getThemeId('frontend/Swissup/argento-luxury'),
                'settings' => [
                    'css_class' => 'navpro-nowrap',
                    'identifier' => 'argento_luxury_topmenu',
                ],
            ]
        ];
    }
}
