<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Swissup\Pagespeed\Model\Optimiser\HTTP2ServerPush;

class LazyLoad extends HTTP2ServerPush
{
    /**
     *
     * @param string $src
     * @return boolean
     */
    protected function isIgnored($src)
    {
        return $this->config->isImageLazyLoadIgnored($src);
    }

    /**
     *
     * @return string
     */
    protected function getPlaceholder()
    {
        return $this->config->isImageLazyLoadPlaceholderEnable() ?
            $this->config->getLazyloadPlaceholder() : '';
    }

    /**
     *
     * @return int
     */
    protected function getOffset()
    {
        $offset = $this->config->isMobile() ?
            $this->config->getLazyloadMobileOffset() : $this->config->getLazyloadOffset();
        $offset = $offset + 1;

        return $offset;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isImageLazyLoadEnable() || $response === null) {
            return $response;
        }
        $html = $response->getBody();

        $_html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $images = array();
        preg_match_all('/<img[\s\r\n]+.*?>/is', $_html, $images);
        unset($_html);
        $images = isset($images[0]) ? $images[0] : [];

        if (empty($images)) {
            return;
        }

        $defaultPlaceholder = $this->getPlaceholder();
        // $placeholder = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
        // $placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP';
        $dom = new \Zend\Dom\Query();
        $offset = $this->getOffset();

        $hrefImg = [];
        foreach ($images as $image) {
            $dom->setDocumentHtml($image);
            $node = $dom->execute('img')->current();
            $hasInjection = false;
            foreach (array('src', 'srcset') as $attrName) {
                $attrValue = $node->getAttribute($attrName);
                if (!empty($attrValue) && !$this->isIgnored($attrValue)) {
                    $dataAttrName = 'data-' . $attrName;
                    $dataAttrValue = $node->getAttribute($dataAttrName);
                    if (empty($dataAttrValue)) {
                        $node->setAttribute($dataAttrName, $attrValue);

                        $placeholder = $defaultPlaceholder;
                        if ($attrName === 'srcset') {
                            $sizesAttrValue = $node->getAttribute('sizes');
                            if (!empty($sizesAttrValue)) {
                                $placeholder = $defaultPlaceholder . ' 1w';
                            }
                        }
                        $node->setAttribute($attrName, $placeholder);
                        $hasInjection = true;
                    }
//                    $node->removeAttribute($attrName);
                }
            }
            if ($hasInjection) {
                //add css class
                $class = $node->getAttribute('class') . ' lazyload';
                $node->setAttribute('class', trim($class));
                //add alt for fix chrome width 0 (auto) bug
                $alt = $node->getAttribute('alt');
                if (empty($alt)) {
                    $node->setAttribute('alt', 'lazyload');
                }
                if (--$offset > 0) {
                    $src = $node->getAttribute('data-src');
                    if (!empty($src)) {
                        $hrefImg[] = $src;
                    }
                    continue;
                }
                $tempDom = new \DOMDocument();
                $clonedNode = $node->cloneNode(true);
                $tempDom->appendChild($tempDom->importNode($clonedNode, true));
                $_image = $tempDom->saveHTML();
                // $_image = utf8_decode($node->C14N())
                $_image = utf8_decode($_image);
                $_image = $this->getUtf8ToHtml($_image);

                $html = str_replace($image, $_image, $html);
            }
        }
        $response->setBody($html);

        if ($this->config->isHTTP2ServerPushForImgEnabled()) {
            $this->HTTP2ServerPush($response, $hrefImg, 'image', 'preload');
        }

        return $response;
    }
}
