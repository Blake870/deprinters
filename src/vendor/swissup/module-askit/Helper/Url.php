<?php
namespace Swissup\Askit\Helper;

// use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
// use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Swissup\Askit\Api\Data\MessageInterface;

class Url extends AbstractHelper
{
    const ENTITY_PRODUCT_URL_PATH_EDIT  = 'catalog/product/edit';
    const ENTITY_CATEGORY_URL_PATH_EDIT = 'catalog/category/edit';
    const ENTITY_PAGE_URL_PATH_EDIT     = 'cms/page/edit';

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Cms page
     *
     * @var \Magento\Cms\Helper\Page
     */
    protected $cmsPageHelper;

    /**
     * @param Context $context
     * @param  \Magento\Catalog\Model\ProductFactory $productFactory
     * @param  \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param  \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Helper\Page $cmsPage
    ) {

        $this->productFactory = $productFactory;
        $this->pageFactory = $pageFactory;
        $this->categoryFactory = $categoryFactory;

        $this->cmsPageHelper = $cmsPage;

        parent::__construct($context);
    }

  /**
   * @param UrlInterface $urlBuilder
   */
    public function setUrlBuilder(UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
        return $this;
    }

    public function get($type, $id, $edit = true)
    {
        $label = $href = false;
        switch ($type) {
            case MessageInterface::TYPE_CMS_PAGE:
                $page = $this->pageFactory->create()
                    ->load($id);
                $label = $page->getTitle();

                $href = $edit ? $this->_urlBuilder->getUrl(
                    self::ENTITY_PAGE_URL_PATH_EDIT,
                    ['page_id' => $id]
                ) : $this->cmsPageHelper->getPageUrl($page->getId());
                break;
            case MessageInterface::TYPE_CATALOG_CATEGORY:
                $category = $this->categoryFactory->create()
                    ->load($id);
                $label = $category->getName();
                $href = $edit ? $this->_urlBuilder->getUrl(
                    self::ENTITY_CATEGORY_URL_PATH_EDIT,
                    ['id' => $id]
                ) : $category->getUrl();
                break;
            case MessageInterface::TYPE_CATALOG_PRODUCT:
            default:
                $product = $this->productFactory->create()
                    ->load($id);
                $label = $product->getName();
                $href = $edit ? $this->_urlBuilder->getUrl(
                    self::ENTITY_PRODUCT_URL_PATH_EDIT,
                    ['id' => $id]
                ) : $product->getUrlModel()->getUrl($product);
                break;
        }

        return ['label' => $label, 'href' => $href];
    }

    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function getGravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = true, $atts = [])
    {
        $url = '//www.gravatar.com/avatar/';
        $url .= hash('md5', strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            if (!array_key_exists('width', $atts)) {
                $atts['width'] = $s;
            }

            if (!array_key_exists('height', $atts)) {
                $atts['height'] = $s;
            }

            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }
        return $url;
    }
}
