<?php

namespace Swissup\EasySlide\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;


class Image extends AbstractHelper
{
    /**
     * Media sub directory
     *
     * @var string
     */
    protected $subDir = 'easyslide/';

    /**
     * Cache (resized) directory
     *
     * @var string
     */
    protected $cacheDir = 'resized/';

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Filesystem         $fileSystem
     * @param \Magento\Framework\Image\Factory      $imageFactory
     * @param Context                               $context
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Image\Factory $imageFactory,
        Context $context
    ) {
        $this->ioFile = $ioFile;
        $this->fileSystem = $fileSystem;
        $this->imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * Return URL for resized image
     *
     * @param $imageFile resize image url
     * @param $width resize image width
     * @param $height resize image height
     * @return bool|string
     */
    public function resize($imageFile, $width, $height = null)
    {
        if (!$imageFile) {
            return false;
        }

        $sizeDir = $this->cacheDir . $width . 'x' . $height . '/';
        $cachePath = $this->getBaseDir() . $sizeDir;
        $cacheUrl = $this->getBaseUrl() . $sizeDir;
        $this->ioFile->checkAndCreateFolder($cachePath);
        $this->ioFile->open(['path' => $cachePath]);
        if ($this->ioFile->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }

        try {
            $image = $this->imageFactory->create($this->getBaseDir() . $imageFile);
            $image->constrainOnly(true);
            $image->keepAspectRatio(true);
            $image->keepFrame(false);
            $image->keepTransparency(true);
            $image->resize($width, $height);
            $image->save($cachePath . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get images base url of easyslide directory
     *
     * @return string
     */
    public function getBaseUrl($type = UrlInterface::URL_TYPE_MEDIA)
    {
        return $this->_urlBuilder
            ->getBaseUrl(['_type' => $type]) . $this->subDir;
    }

    /**
     * Get base image dir
     *
     * @return string
     */
    public function getBaseDir($directoryCode = DirectoryList::MEDIA)
    {
        return $this->fileSystem
            ->getDirectoryWrite($directoryCode)
            ->getAbsolutePath($this->subDir);
    }

    /**
     * Get width of image
     *
     * @param  string $imageFile
     * @return int
     */
    public function getImageWidth($imageFile)
    {
        $width = 0;
        $imagePath = $this->getBaseDir() . $imageFile;
        if ($this->ioFile->fileExists($imagePath)) {
            list($width) = getimagesize($imagePath);
        }

        return $width;
    }

    /**
     * Delete image file from $directory or easyslide baseDir
     *
     * @param  string $imageFile
     * @param  string $directory Absolute path
     * @return bool
     */
    public function delete($imageFile, $directory = null)
    {
        $filePath = ($directory ?: $this->getBaseDir()) . $imageFile;
        return $this->ioFile->rm($filePath);
    }

    /**
     * Clean resized cached images
     *
     * @param string $imageFile
     */
    public function cleanCached($imageFile)
    {
        $cachePath = $this->getBaseDir() . $this->cacheDir;
        foreach ($this->ioFile->getDirectoriesList($cachePath) as $directory) {
            $this->delete($imageFile, $directory . '/');
        }
    }
}
