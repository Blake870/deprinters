<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Swissup\Pagespeed\Helper\Config;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Swissup\Pagespeed\Model\Optimiser\Image\WebP as WebPOptimiser;

class SpecifyDimension extends WebPOptimiser
{
    /**
     * @url https://github.com/marc1706/fast-image-size
     *
     * @var \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface
     */
    private $imageSize;

    /**
     * @param Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param MediaConfig $mediaConfig
     * @param Filesystem $filesystem
     * @param \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface $imageSize
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        MediaConfig $mediaConfig,
        Filesystem $filesystem,
        \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface $imageSize
    ) {
        parent::__construct($config, $cache, $cacheState, $serializer, $mediaConfig, $filesystem);

        $this->imageSize = $imageSize;
    }

    /**
     *
     * @param  string $src
     * @return mixed|boolean|array
     */
    protected function getDimensions($src)
    {
        $dimensions = $this->loadCache($src);
        if ($dimensions === false) {
            $dimensions = $this->imageSize->getDimensions($src);
            if ($dimensions === false) {
                $filePath = $this->getImageAbsolutePath($src);
                if ($filePath) {
                    $dimensions = $this->imageSize->getDimensions($filePath);
                } else {
                    $dimensions = getimagesize($src);
                    if (!$dimensions) {
                        return false;
                    }
                    $dimensions['width'] = $dimensions[0];
                    // unset($dimensions[0]);
                    $dimensions['height'] = $dimensions[1];
                    // unset($dimensions[1]);
                }
            }
            $this->saveCache($src, $dimensions);
        }

        return $dimensions;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean|string
     */
    private function getImageAbsolutePath($imageUrl)
    {
        $relativePath = false;
        $mediaUrl = $this->getMediaProductUrl();
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);

        if (strpos($imagePath, $mediaUrl) === 0) {
            $relativeFilename = substr_replace($imagePath, '', 0, strlen($mediaUrl));
            $relativePath = $this->mediaConfig->getMediaPath($relativeFilename);
        }

        $targetDirs = $this->config->getResizeCommandTargetDirs();
        foreach ($targetDirs as $targetDir) {
            if (strpos($imagePath, "/media/{$targetDir}/") === 0) {
                $relativeFilename = false;
                $relativePath = substr_replace($imagePath, '', 0, strlen('/media/'));
                break;
            }
        }

        return $this->mediaDirectoryRead->getAbsolutePath($relativePath);
    }


    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDimensionEnable() || $response === null) {
            return $response;
        }
        $html = $response->getBody();

        $matches = array();
        preg_match_all('/<img[\s\r\n]+.*?>/is', $html, $matches);
        $dom = new \Zend\Dom\Query();
        $images = isset($matches[0]) ? $matches[0] : [];

        foreach ($images as $image) {
            $dom->setDocumentHtml($image);
            $node = $dom->execute('img')->current();

            $_attrValueFlag = false;
            foreach (array('height', 'width') as $_attributeName) {
                $_attrValue = $node->getAttribute($_attributeName);
                if (!empty($_attrValue)) {
                    $_attrValueFlag = true;
                    break;
                }
            }
            if ($_attrValueFlag) {
                continue;
            }

            foreach (array('src', 'data-src') as $attrName) {
                $attrValue = $node->getAttribute($attrName);
                if (empty($attrValue)) {
                    continue;
                }
                $dimensions = $this->getDimensions($attrValue);
                if (!$dimensions) {
                    continue;
                }

                if (isset($dimensions['width'])) {
                    $node->setAttribute('width', $dimensions['width']);
                }
                if (isset($dimensions['height'])) {
                    $node->setAttribute('height', $dimensions['height']);
                }
                $_image = utf8_decode($node->C14N());
                $_image = $this->getUtf8ToHtml($_image);
                $_image = str_replace('></img>', ' />', $_image);

                if (strpos($_image, '="\"') !== false) {
                    continue;
                }
                $html = str_replace($image, $_image, $html);
            }
        }

        $response->setBody($html);

        return $response;
    }
}
