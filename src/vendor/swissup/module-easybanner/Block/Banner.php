<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\Easybanner\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Banner extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'banner.phtml';

    /**
     * @var \Swissup\Easybanner\Model\Banner
     */
    private $banner;

    /**
     * @var \Swissup\Easybanner\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Template\Context $context
     * @param \Swissup\Easybanner\Model\BannerFactory $bannerFactory
     * @param \Swissup\Easybanner\Helper\Image $imageHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Swissup\Easybanner\Model\BannerFactory $bannerFactory,
        \Swissup\Easybanner\Helper\Image $imageHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->banner = $bannerFactory->create();
        $this->imageHelper = $imageHelper;
        $this->objectManager = $objectManager;

        parent::__construct($context, $data);
    }

    public function getBannerData()
    {
        if ($this->getBannerObject()) {
            $this->banner = $this->getBannerObject();
        } else {
            $bannerId = $this->getBanner();
            if (!$bannerId) {
                return false;
            }
            $this->banner->load($bannerId);
        }

        if (!$this->banner->getId() || !$this->banner->getStatus()) {
            return false;
        }

        $storeId = $this->_storeManager->getStore()->getId();
        if (!$this->banner->isVisible($storeId)) {
            return false;
        }

        $statistic = $this->objectManager->create('Swissup\Easybanner\Model\BannerStatistic');
        $statistic->incrementDisplayCount($this->banner->getId());

        return $this->banner;
    }

    public function getBannerUrl()
    {
        $url = 'easybanner/click/index/id/' . $this->banner->getId();
        if (!$this->banner->getHideUrl()) {
            $url .= '/url/' . trim($this->banner->getUrl(), '/');
        }

        return $url;
    }

    public function getBannerImage()
    {
        if (!$image = $this->banner->getImage()) {
            return false;
        }

        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl . 'easybanner/' . ltrim($image, '/');
    }

    public function resizeImage($width, $height)
    {
        return $this->imageHelper->resize($this->banner, $width, $height);
    }

    public function getBannerHtml()
    {
        $bannerHtml = $this->banner->getHtml();
        $bannerHtml = str_replace('{{tm_banner_url}}', $this->getUrl($this->getBannerUrl()), $bannerHtml);
        $bannerHtml = str_replace('{{swissup_easybanner_url}}', $this->getUrl($this->getBannerUrl()), $bannerHtml);
        $cmsFilter = $this->objectManager->get('Magento\Cms\Model\Template\FilterProvider');
        $storeId = $this->_storeManager->getStore()->getId();
        $html = $cmsFilter->getBlockFilter()
            ->setStoreId($storeId)
            ->filter($bannerHtml);

        return $html;
    }

    public function getSystemClassName()
    {
        $class = 'easybanner-banner';

        if ($this->banner->isPopupType()) {
            $class .= ' placeholder-' . $this->banner->getTypeCode();
        }

        return $class;
    }

    public function getClassName()
    {
        $class = $this->getSystemClassName();

        $class .= ' ' . $this->banner->getHtmlId();

        if ($this->banner->getClassName()) {
            $class .= ' ' . $this->banner->getClassName();
        }

        if ($this->banner->getAdditionalCssClass()) {
            $class .= ' ' . $this->banner->getAdditionalCssClass();
        }

        if ($this->getAdditionalCssClass()) {
            $class .= ' ' . $this->getAdditionalCssClass();
        }

        return $class;
    }
}
