<?php
namespace Swissup\Lightboxpro\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Swissup\Lightboxpro\Model\Gallery\Media\Config as GalleryConfig;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * Image factory
     *
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        $this->ioFile = $ioFile;
        $this->fileSystem = $fileSystem;
        $this->imageFactory = $imageFactory;

        parent::__construct($context);
    }

    /**
     * Return URL for resized image
     *
     * @param string $imageFile
     * @param int $width
     * @param int $height
     * @return bool|string
     */
    public function resize($imageFile, $width, $height)
    {
        $cacheDir  = $this->getBaseDir() . '/' . 'cache' . '/' . $width;
        $cacheUrl  = $this->getBaseUrl() . '/' . 'cache' . '/' . $width . '/';

        $io = $this->ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }
        try {
            $image = $this->imageFactory->create($this->getBaseDir() . '/' . $imageFile);
            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);

            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return the base media directory testimonial images
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath(GalleryConfig::UPLOAD_FOLDER);
    }

    /**
     * Return the Base URL for News Item images
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder
            ->getBaseUrl([
                '_type' => UrlInterface::URL_TYPE_MEDIA
            ]) . GalleryConfig::UPLOAD_FOLDER;
    }
}
