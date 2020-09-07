<?php
namespace Swissup\Lightboxpro\Model\Gallery\Media;

class Config extends \Magento\Catalog\Model\Product\Media\Config
{
    /**
     * Folder name to upload images
     */
    const UPLOAD_FOLDER = 'lightboxpro';

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return self::UPLOAD_FOLDER;
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::UPLOAD_FOLDER;
    }
}
