<?php
namespace Swissup\Amp\Helper;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $dimensions = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \FastImageSize\FastImageSize
     */
    protected $remoteImage;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \FastImageSize\FastImageSize $remoteImage
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        \FastImageSize\FastImageSize $remoteImage
    ) {
        $this->fileSystem = $fileSystem;
        $this->remoteImage = $remoteImage;
        parent::__construct($context);
    }

    /**
     * Get image dimensions
     *
     * 1. Try to locate image locally and use getimagesize
     * 2. Use FastImageSize lib to detect remote image dimensions
     *
     * @param  string $path public url
     * @return array
     */
    public function getDimensions($path)
    {
        if (empty($this->dimensions[$path])) {
            // 1. Try to locate image locally and use getimagesize
            $localPath = str_replace(
                [
                    $this->_urlBuilder->getBaseUrl(),
                    $this->_urlBuilder->getBaseUrl([
                        '_type' => UrlInterface::URL_TYPE_LINK,
                        '_secure' => true
                    ])
                ],
                $this->fileSystem
                    ->getDirectoryRead(DirectoryList::ROOT)
                    ->getAbsolutePath(),
                $path
            );

            if (file_exists($localPath)) {
                $dimensions = getimagesize($localPath);
            } else {
                // 2. Use FastImageSize lib to detect remote image dimensions
                $dimensions = $this->remoteImage->getImageSize($path);
                if ($dimensions) {
                    $dimensions = array_values($dimensions);
                }
            }

            $this->dimensions[$path] = $dimensions ? $dimensions : [false, false];
        }

        return $this->dimensions[$path];
    }

    /**
     * Get image width
     *
     * @param  string $path
     * @return string
     */
    public function getWidth($path)
    {
        $dimensions = $this->getDimensions($path);

        return $dimensions[0];
    }

    /**
     * Get image height
     *
     * @param  string $path
     * @return string
     */
    public function getHeight($path)
    {
        $dimensions = $this->getDimensions($path);

        return $dimensions[1];
    }
}
