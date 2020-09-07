<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class DeferCss extends HTTP2ServerPush
{
    /**
     *
     * @return string
     */
    public function getUnpackScript()
    {
        return implode("\n", array(
            '<script>',
                'var loadDeferredStyles = function() {',
                '    var addStylesNode = document.getElementById("deferred-styles");',
                '    var replacement = document.createElement("div");',
                '    replacement.innerHTML = addStylesNode.textContent;',
                '    document.body.appendChild(replacement);',
                '    addStylesNode.parentElement.removeChild(addStylesNode);',
                '};',
                'var raf = requestAnimationFrame || mozRequestAnimationFrame ||',
                    'webkitRequestAnimationFrame || msRequestAnimationFrame;',
                'if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });',
                'else window.addEventListener("load", loadDeferredStyles);',
            '</script>' . "\n"
        ));
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDeferCssEnable() || $response === null) {
            return $response;
        }

        $html = $response->getBody();

        $dom = $this->getDomDocument($html);
        $xpath = new \DOMXPath($dom);

        $links = $infoLinks = $pushLinkArray = array();
        $isLoadCssEnable = $this->config->isDeferCssOnloadEnable();
        $xpathString = '//link[@rel="stylesheet" or @media="all" or @media="screen"]';
        $nodes = $xpath->query($xpathString);
        $mediaAttr = $hrefAttr = $relAttr = $typeAttr = $onloadAttr = false;
        foreach ($nodes as $node) {
            $link = $this->getNodeString($node);
            // if (!$this->isIgnore($link)) {
            $relAttr    = $node->getAttribute('rel');
            $typeAttr   = $node->getAttribute('type');
            $mediaAttr  = $node->getAttribute('media');
            $hrefAttr   = $node->getAttribute('href');
            $onloadAttr = $node->getAttribute('onload');

            if ($isLoadCssEnable && $relAttr != 'preload' && empty($onloadAttr)) {
                $node->setAttribute('rel', 'preload');
                $node->setAttribute('as', 'style');
                $node->setAttribute('onload', "this.onload=null;this.rel='stylesheet'");
                $node->setAttribute('onerror', "this.onerror=null;this.rel='stylesheet'");
                $node->removeAttribute('type');
                // $link = str_replace(
                //     array('type="text/css"', "type='text/css'"),
                //     '',
                //     $link
                // );
            } else {
                $node->parentNode->removeChild($node);
            }

            $links[] = $link;

            if ($mediaAttr !== 'print') {
                $pushLinkArray[] = $hrefAttr;
            }

            $to = $this->getNodeString($node);
            $to = trim($to, "\r\n ");
            $to = str_replace('>', ' />', $to);

            $regex = '/<link\n?.*' .
                // 'rel="'   . preg_quote($relAttr)       . '".+' .
                // 'type="'  . preg_quote($typeAttr, '/') . '".+' .
                // 'media="' . preg_quote($mediaAttr)     . '".+' .
                'href="'  . preg_quote($hrefAttr, '/') . '".*?>/i';

            $infoLinks[] = [
                'rel'   => $relAttr,
                'type'  => $typeAttr,
                'media' => $mediaAttr,
                'href'  => $hrefAttr,
                'from'  => $link,
                'to'    => $to,
                'regex' => empty($hrefAttr) ? false : $regex
            ];
        }

        $regExp = '/<link\b[^>]*>/is';
        $allLinkElementAtPage = array();
        preg_match_all($regExp, $html, $allLinkElementAtPage);
        foreach ($allLinkElementAtPage[0] as $linkElement) {
            foreach ($infoLinks as &$linkInfo) {
                $hrefPosition  = empty($linkInfo['href']) ? false : strpos($linkElement, $linkInfo['href']);
                $relPosition   = empty($linkInfo['rel']) ? false : strpos($linkElement, $linkInfo['rel']);
                $typePosition  = empty($linkInfo['type']) ? false : strpos($linkElement, $linkInfo['type']);
                $mediaPosition = empty($linkInfo['media']) ? false : strpos($linkElement, $linkInfo['media']);

                if ($hrefPosition > 5 &&
                   ($relPosition > 5 || strpos($linkElement, 'rel=') === false) &&
                   ($typePosition > 5 || strpos($linkElement, 'type=') === false) &&
                   ($mediaPosition > 5 || strpos($linkElement, 'media=') === false)
                ) {
                    $linkInfo['origin'] = $linkElement;
                }
            }
        }
        foreach ($infoLinks as $linkInfo) {
            // $html = str_replace($linkInfo['from'], $linkInfo['to'], $html);
            if (isset($linkInfo['origin'])) {
                $html = str_replace($linkInfo['origin'], $linkInfo['to'], $html);
            } elseif (!empty($linkInfo['regex'])) {
                $html = preg_replace(
                    $linkInfo['regex'],
                    $isLoadCssEnable ? $linkInfo['to'] : '',
                    $html
                );
            }
        }
        // $html = $this->getSaveHTML($dom);
        $links = "\t" . trim(implode("\t", $links));

        if (!empty($links)) {
            $links = array(
                '<noscript id="deferred-styles">',
                    $links,
                '</noscript>',
            );
        }
        if (!$isLoadCssEnable) {
            $links[] = $this->getUnpackScript();
        }

        $links = implode("\n", $links);
        $html = str_replace('</body>', $links . '</body>', $html);

        $response->setBody($html);

        if ($this->config->isHTTP2ServerPushForCssEnabled()) {
            $this->HTTP2ServerPush($response, $pushLinkArray, 'style', 'preload');
        }

        return $response;
    }

    /**
     *
     * @param  DOMElement $node
     * @return string
     */
    protected function getNodeString($node)
    {
        $tempDom = new \DOMDocument();
        $clonedNode = $node->cloneNode(true);
        $tempDom->appendChild($tempDom->importNode($clonedNode, true));
        return $tempDom->saveHTML();
    }
}
