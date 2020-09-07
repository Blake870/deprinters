<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Swissup\Pagespeed\Model\Optimiser\Image\SpecifyDimension as SpecifyDimensionOptimiser;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssertImageFactory;

class Responsive extends SpecifyDimensionOptimiser
{
    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isImageResponsiveEnable() || $response === null) {
            return $response;
        }
        \Magento\Framework\Profiler::start(__METHOD__);
        $html = $response->getBody();

        $matches = array();
        preg_match_all('/<img[\s\r\n]+.*?>/is', $html, $matches);
        $dom = new \Zend\Dom\Query();
        $images = isset($matches[0]) ? $matches[0] : [];
        // $allowedExtensions = explode(',', 'jpeg,jpg,png');

        // $resolutions = [
        //     // '160w', '240w', '320w', '640w', '1280w'
        //     '160', '240', '320', '640', '1280'
        // ];
        //
        $productResolutions = [0.5 ,0.75, 1, 2, 3];
        $cmsResolutions = [0.5 ,0.75, 1];
        $defaultSizes = $this->config->getDefaultImageResponsiveSizes();
        foreach ($images as $image) {
            $dom->setDocumentHtml($image);
            $node = $dom->execute('img')->current();

            $srcsetValue = $node->getAttribute('srcset');
            if (!empty($srcsetValue)) {
                continue;
            }

            $srcValue = $node->getAttribute('src');
            if (empty($srcValue)) {
                continue;
            }

            $isMediaCustomUrl = $this->isMediaCustomUrl($srcValue);
            if (!$this->isMediaProductUrl($srcValue)
                && !$isMediaCustomUrl
            ) {
                continue;
            }
            $basename = basename($srcValue);
            // $filename = pathinfo($basename, PATHINFO_BASENAME);
            $dimensions = $this->getDimensions($srcValue);
            if (!$dimensions) {
                continue;
            }

            $sizes = [];
            $srcset = [];
            $width = $dimensions['width'];
            $resolutions = $isMediaCustomUrl ? $cmsResolutions : $productResolutions;

            // https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
            // The browser ignores everything after the first matching condition, so be careful how you order the media conditions.
            $sizes['max-width: 480px'] = $isMediaCustomUrl ? '(max-width: 480px) 100vw' : '(max-width: 480px) 50vw';

            foreach ($resolutions as $resolution) {
                if ($resolution !== 1) {
                    $urlPath = str_replace($basename, $resolution . 'x' . DIRECTORY_SEPARATOR . $basename, $srcValue);
                } else {
                    $urlPath = $srcValue;
                }
                if ($this->isImageFileExist($urlPath, false) === false) {
                    continue;
                }
                $size = ceil($width * $resolution);
                // $srcset[$resolution . 'x'] = $urlPath . ' ' . $resolution . 'x';
                $srcset[$resolution . 'w'] = $urlPath . ' ' . $size . 'w';
                $sizes[$resolution] = "(max-width: " . $size .  "px) " . ($size - 40)  . 'px';
            }
            // $sizes[] = '(max-width: 768px) ' . ceil($width * 3 / 4) . 'px';
            $sizes[$resolution] = $width . 'px';
            $sizes = empty($defaultSizes) || $isMediaCustomUrl ? implode(',', $sizes) : $defaultSizes;

            $node->setAttribute('srcset', implode(',', $srcset));
            $node->setAttribute('sizes', $sizes);

            $_image = utf8_decode($node->C14N());
            $_image = $this->getUtf8ToHtml($_image);
            $_image = str_replace('></img>', ' />', $_image);

            if (strpos($_image, '="\"') !== false) {
                continue;
            }
            $html = str_replace($image, $_image, $html);
        }

        $response->setBody($html);
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $response;
    }
}
