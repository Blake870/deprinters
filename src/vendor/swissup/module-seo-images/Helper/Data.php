<?php

namespace Swissup\SeoImages\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\RuntimeException;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'seo_images/general/enabled';
    const XML_PATH_CLEAR_PARAMS = 'seo_images/misc_string/clear_params';
    const XML_PATH_PRODUCT_TEMP = 'seo_images/image_name/product_image';

    /**
     * @var \Swissup\SeoImages\Model\EntityFactory
     */
    protected $seoImageFactory;

    /**
     * @param \Swissup\SeoImages\Model\EntityFactory  $seoImageFactory
     * @param Context                                 $context
     */
    public function __construct(
        \Swissup\SeoImages\Model\EntityFactory $seoImageFactory,
        Context $context
    ) {
        $this->seoImageFactory = $seoImageFactory;
        parent::__construct($context);
    }

    /**
     * Inspired by and bounded with \Magento\MediaStorage\App\Media::getOriginalImage
     * Works with path as well as with url.
     *
     * @param  string $path
     * @return string
     */
    public function buildFileKey($path) {
        return preg_replace('|^.*((?:/[^/]+){3})$|', '$1', $path);
    }

    /**
     * Save SEO name for image when it is not empty
     *
     * @param  string $fileKey
     * @param  string $originalFile
     * @param  string $targetFile
     * @return string
     */
    public function saveSeoImage($fileKey, $originalFile, $targetFile)
    {
        if ($targetFile) {
            $this->seoImageFactory->create()
                ->load($fileKey, 'file_key')
                ->setFileKey($fileKey)
                ->setOriginalFile($originalFile)
                ->setTargetFile($targetFile)
                ->save();
        }
    }

    /**
     * Get template for name if image
     *
     * @return string
     */
    public function getSeoImageNameTemplate()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_TEMP);
    }

    /**
     * Is module enabled in Stores - Conguration
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Is replace hash with clear params enabled.
     *
     * @return boolean
     */
    public function isClearParams()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CLEAR_PARAMS);
    }

    /**
     * Can change image name? Module enabled and template is not empty.
     *
     * @return boolean
     */
    public function canChangeName()
    {
        return $this->isEnabled() && $this->getSeoImageNameTemplate();
    }
}
