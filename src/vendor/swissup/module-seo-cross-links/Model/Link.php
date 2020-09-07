<?php

namespace Swissup\SeoCrossLinks\Model;

class Link extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const SEARCH_IN_ALL = 0;
    const SEARCH_IN_СATEGORY = 1;
    const SEARCH_IN_PRODUCT = 2;
    const SEARCH_IN_CMS = 3;

    const URL_DESTINATION_CUSTOM = 0;
    const URL_DESTINATION_CATEGORY = 1;
    const URL_DESTINATION_PRODUCT = 2;
    const URL_DESTINATION_CMS_PAGE = 3;

    /**
     * @var style classes
     */
    const CUSTOM_CLASS = 'crosslink-default';
    const ANIMATION_UNDERLINE_HOVER_CLASS = 'crosslink-underline-animation';
    const BACKGROUND_HOVER_CLASS = 'crosslink-highlight-background';
    const ANIMATION_COLOR_HOVER_CLASS = 'crosslink-color-animation';


    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Swissup\SeoCrossLinks\Model\ResourceModel\Link::class);
    }

    public function getUrl()
    {
        $url = $this->getUrlPath();
        if ($url === '#'
            || strpos($url, 'http://') === 0
            || strpos($url, 'https://') === 0) {

            return $url;
        }

        $array = str_split($url);
        $symbols = ['.', '?', '#'];
        if (array_intersect($array, $symbols)) {
            if (strpos($url, '#') === 0) {
                return $url;
            }
            $url = $this->urlBuilder->getDirectUrl($url);
        } else {
            $url = $this->urlBuilder->getUrl($url);
        }

        return $url;
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
        ];
    }

    public function getSearchIn()
    {
        return [
            self::SEARCH_IN_ALL => __('All'),
            self::SEARCH_IN_СATEGORY => __('Category Description'),
            self::SEARCH_IN_PRODUCT => __('Product Description'),
            self::SEARCH_IN_CMS => __('CMS Page Content')

        ];
    }

    public function getDestination()
    {
        return [
            self::URL_DESTINATION_CUSTOM => __('Custom Page'),
            self::URL_DESTINATION_CATEGORY => __('Category Page'),
            self::URL_DESTINATION_PRODUCT => __('Product Page'),
            self::URL_DESTINATION_CMS_PAGE => __('CMS Page')
        ];
    }
    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * @var Style classes
     *@return  array
     */
    public function getStyleClass()
    {
        return [
            self::CUSTOM_CLASS => __('Default'),
            self::ANIMATION_UNDERLINE_HOVER_CLASS => __('Animation underline'),
            self::BACKGROUND_HOVER_CLASS => __('Highlight background'),
            self::ANIMATION_COLOR_HOVER_CLASS => __('Animation color and underline')
        ];
    }
}
