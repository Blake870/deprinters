<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Swissup\Pagespeed\Helper\Config;
use Swissup\Pagespeed\Model\Optimiser\AbstractCachableOptimiser;

class WebP extends AbstractCachableOptimiser
{
    /**
     * Cache state name
     */
    const CACHE_STATE = 'SW_PS_WEBP_IS_FILE_EXIST';

    /**
     *
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectoryRead;

    /**
     *
     * @var string
     */
    protected $mediaUrl;

    /**
     * @param Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param MediaConfig $mediaConfig
     * @param Filesystem $filesystem
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        MediaConfig $mediaConfig,
        Filesystem $filesystem
    ) {
        $this->mediaConfig = $mediaConfig;
        $this->mediaDirectoryRead = $filesystem->getDirectoryRead(DirectoryList::MEDIA);

        parent::__construct($config, $cache, $cacheState, $serializer);
    }

    /**
     *
     * @return string
     */
    protected function getMediaProductUrl()
    {
        if ($this->mediaUrl === null) {
            $this->mediaUrl = $this->mediaConfig->getBaseMediaUrl();
            $this->mediaUrl = parse_url($this->mediaUrl, PHP_URL_PATH);
        }

        return $this->mediaUrl;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function isMediaProductUrl($imageUrl)
    {
        $mediaUrl = $this->getMediaProductUrl();
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);

        return strpos($imagePath, $mediaUrl) === 0;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function isMediaCustomUrl($imageUrl)
    {
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);
        // return strpos($imagePath, '/media/wysiwyg/') === 0;

        $targetDirs = $this->config->getResizeCommandTargetDirs();
        foreach ($targetDirs as $targetDir) {
            if (strpos($imagePath, "/media/{$targetDir}/") !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function isImageFileExist($imageUrl, $useCache = true)
    {
        // $return = $useCache ? $this->loadCache($imageUrl) : false;
        $return = $useCache ? $this->loadCacheLayerValue(self::CACHE_STATE, $imageUrl) : false;
        if ($return === false) {
            $return = $this->_isImageFileExist($imageUrl);
            if ($useCache) {
                // $this->saveCache($imageUrl, $return ? 1 : 0);
                $this->setCacheLayerValue(self::CACHE_STATE, $imageUrl, $return ? 1 : 0);
            }
        }

        return (bool) $return;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function _isImageFileExist($imageUrl)
    {
        $relativePath = false;
        $mediaUrl = $this->getMediaProductUrl();
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);

        if (strpos($imagePath, $mediaUrl) === 0) {
            $relativeFilename = substr_replace($imagePath, '', 0, strlen($mediaUrl));
            $relativePath = $this->mediaConfig->getMediaPath($relativeFilename);
        }

        $targetDirs = $this->config->getResizeCommandTargetDirs();
        $targetDirs[] = '.';

        foreach ($targetDirs as $targetDir) {
            $targetDirCheckpoint = "/media/{$targetDir}/";
            if ($targetDir === '.') {
                $targetDirCheckpoint = '/media/';
            }
            if (strpos($imagePath, $targetDirCheckpoint) !== false) {
                // $relativeFilename = false;
                list(, $relativePath) = explode('/media/', $imagePath);
                break;
            }
        }

        return $relativePath && $this->mediaDirectoryRead->isFile($relativePath);
    }

    /**
     *
     * @param  string $html
     * @param  string $imageHTML
     * @return bool
     */
    private function isParentTagPicture($html, $imageHTML)
    {
        $position = strpos($html, $imageHTML);
        $subHtml = substr($html, $position, 500);
        $length = strpos($subHtml, '</');
        $subHtml = substr($subHtml, $length);
        $length = strpos($subHtml, '>');
        $parentTag = substr($subHtml, 2, $length - 2);

        return $parentTag === 'picture' ;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isWebPSupport() || $response === null) {
            return $response;
        }
        \Magento\Framework\Profiler::start(__METHOD__);
        $html = $response->getBody();

        $matches = array();
        preg_match_all('/<img[\s\r\n]+.*?>/is', $html, $matches);
        $dom = new \Zend\Dom\Query();
        $images = isset($matches[0]) ? $matches[0] : [];
        $isAddPictureTag = $this->config->isWebPAddPictureTag();
        $isImageLazyLoadEnable = $this->config->isImageLazyLoadEnable();
        $_imageHTML = '';
        $srcAttributes = array('src', 'data-src', 'srcset', 'data-src-desktop', 'data-src-retina');

        foreach ($images as $imageHTML) {
            $_imageHTML = $imageHTML;
            $_imageHTML = preg_replace('/\\\\/', '', $_imageHTML);
            $hasSlashes = $_imageHTML !== $imageHTML;

            $dom->setDocumentHtml($_imageHTML);
            $node = $dom->execute('img')->current();

            $hasWebPImageUrl = false;
            foreach ($srcAttributes as $attrName) {
                $attrValue = $node->getAttribute($attrName);
                if (empty($attrValue)) {
                    continue;
                }
                $imageUrls = explode(',', $attrValue);
                $webPImageUrl = false;

                foreach ($imageUrls as $imageUrlPart) {
                    $imageUrlPart = trim($imageUrlPart);
                    list($imageUrl, ) = explode(' ', $imageUrlPart, 2);

                    $webPImageUrl = $this->getWebPImageUrl($imageUrl);
                    if (empty($webPImageUrl)) {
                        continue;
                    }

                    // $headers = @get_headers($webPImageUrl);
                    // if ($headers === false || strpos($headers[0], '404') !== false) {
                    //     continue;
                    // }
                    $hasWebPImageUrl = true;
                    $attrValue = str_replace($imageUrl, $webPImageUrl, $attrValue);
                }

                $node->setAttribute($attrName, $attrValue);
            }
            $newImageHTML = utf8_decode($node->C14N());
            $newImageHTML = $this->getUtf8ToHtml($newImageHTML);
            $newImageHTML = str_replace('></img>', ' />', $newImageHTML);

            if ($isAddPictureTag &&
                $hasWebPImageUrl &&
                $webPImageUrl &&
                $imageHTML !== $newImageHTML
            ) {
                $isImgAlreadyInsidePicture = $this->isParentTagPicture($html, $imageHTML)/* || ($node->parentNode && $node->parentNode->nodeName === 'picture')*/;

                // https://github.com/aFarkas/lazysizes/blob/master/plugins/custommedia/README.md
                $srcsetPrefix = $isImageLazyLoadEnable && $this->config->isImageLazyLoadIgnored($imageUrl) === false ? 'data-' : '';
                $filename = basename($imageUrl);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $newImageHTML = ($isImgAlreadyInsidePicture ? '' : '<picture>') .
                    "<source type=\"image/webp\" {$srcsetPrefix}srcset=\"{$webPImageUrl}\">" .
                    "<source type=\"image/{$extension}\" {$srcsetPrefix}srcset=\"{$imageUrl}\">" .
                    // $newImageHTML .
                    $imageHTML .
                ($isImgAlreadyInsidePicture ? '' : '</picture>');
            }
            if ($hasSlashes) {
                $newImageHTML = addslashes($newImageHTML);
                $newImageHTML = str_replace('/', '\/', $newImageHTML);
            }
            if (strpos($newImageHTML, '="\"') !== false) {
                continue;
            }
            $html = str_replace($imageHTML, $newImageHTML, $html);
        }

        if ($this->config->isReplaceWebPInJs()) {
            $regExpForXMagentoInit = '/<script.*type=\"(text\/x-magento-init).*>(.*)<\/script>/sU';
            $regExpForImageUrl = "/(?:https?:\\\\\/\\\\\/[^\/\s]+\/\S+\.(?:jpe?g|png))/sU";
            $matches = $matches2 = $jsReplacements = [];

            preg_match_all($regExpForXMagentoInit, $html, $matches);

            foreach ($matches[0] as $xMagentoInitContent) {
                preg_match_all($regExpForImageUrl, $xMagentoInitContent, $matches2);
                foreach ($matches2[0] as $imageUrl) {
                    $encodedImageUrl = str_replace('\/', '/', $imageUrl);

                    $webPImageUrl = $this->getWebPImageUrl($encodedImageUrl);
                    if (empty($webPImageUrl)) {
                        continue;
                    }

                    $webPImageUrl = str_replace('/', '\/', $webPImageUrl);
                    $jsReplacements[$imageUrl] = $webPImageUrl;
                }
            }
            $html = str_replace(array_keys($jsReplacements), $jsReplacements, $html);
            // $html = strtr($html, $jsReplacements);
        }
        $this->saveCacheLayer(self::CACHE_STATE);

        $response->setBody($html);
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $response;
    }

    /**
     *
     * @param  string $imageUrl
     * @return string|null
     */
    private function getWebPImageUrl($imageUrl)
    {
        $allowedExtensions = explode(',', 'jpeg,jpg,png');

        $filename = basename($imageUrl);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return;
        }

        $filenames = [
            pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension . '.webp',
            pathinfo($filename, PATHINFO_FILENAME) . '.webp'
        ];

        foreach ($filenames as $newFilename) {
            $webPUrl = str_replace('/' . $filename, '/' . $newFilename, $imageUrl);

            if (($this->isMediaProductUrl($webPUrl)
                || $this->isMediaCustomUrl($webPUrl))
                && $this->isImageFileExist($webPUrl)
            ) {
                return $webPUrl;
            }
        }

        return;
    }
}
