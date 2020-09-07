<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class DeferJs extends HTTP2ServerPush
{

    /**
     *
     * @return string
     */
    public function getDelayscriptType()
    {
        return 'text/defer-javascript';
    }

    /**
     *
     * @return string
     */
    public function getUnpackScript()
    {
        return "<script type=\"text/javascript\">(function (){
    var
        _scripts = document.getElementsByTagName(\"script\"),
        _doc = document,
        _txt = \"" . $this->getDelayscriptType() . "\",
        _isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),
        _textNode
    ;
    for(var i=0,l=_scripts.length;i<l;i++){
        var _type = _scripts[i].getAttribute(\"type\");
        if(_type && _type.toLowerCase() == _txt && _scripts[i].parentNode) {
            try {
                _scripts[i].parentNode.replaceChild((function(sB){
                    if (!_isChrome) {
                        _textNode = document.createTextNode(sB.innerHTML);
                        sB = document.createElement('script');
                        sB.appendChild(_textNode);
                    }

                    sB.type = 'text/javascript';
                    return sB;

                })(_scripts[i]), _scripts[i]);
            } catch(e) {
                console.error(e.name);
                console.error(e.message);
                console.error(e.stack);
                console.error(_scripts[i]);
                throw e;
            }
        }
    }
})();
</script>";
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDeferJsEnable() || $response === null) {
            return $response;
        }
        $html = $response->getBody();

        $dom = $this->getDomDocument($html);
        $xpath = new \DOMXPath($dom);

        $scripts = $hrefSrc = array();
        $xpathString = '//script';
        $isUnpackScript = $this->config->isDeferJsUnpackEnable();
        $delayscriptType = $this->getDelayscriptType();
        $i = 0;
        $j = 0;
        $delaySource = false;
        $offset = $this->config->isJsMergeEnable() ? 2 : 5;
        $ignorePlaceholder = 'type="text/x-magento-template"';
        while ($node = $xpath->query($xpathString)->item($i)) {
            $tempDom = new \DOMDocument();
            $clonedNode = $node->cloneNode(true);
            $src = $clonedNode->getAttribute('src');
            if (!empty($src)) {
                $hrefSrc[] = $src;
            }
            $type = $clonedNode->getAttribute('type');
            if (empty($type)) {
                $clonedNode->setAttribute('type', 'text/javascript');
                $type = $clonedNode->getAttribute('type');
            }

            if ($type === 'text/javascript'
                && (empty($src) || $delaySource)
                && $isUnpackScript
                && $j >= $offset
            ) {
                $clonedNode->setAttribute('type', $delayscriptType);
                $clonedNode->setAttribute('async', 'async');
                $clonedNode->setAttribute('defer', 'defer');
            }
            $tempDom->appendChild($tempDom->importNode($clonedNode, true));
            $_script = $tempDom->saveHTML();
            if (strpos($_script, $ignorePlaceholder) !== false) {
                $i++;
                continue;
            }
            if ((strstr($_script, 'x-magento') || strstr($_script, 'text/x-custom-template'))
                && strstr($_script, '<\/')
                && strstr($_script, '<\/script>') === false
            ) {
                $_script = str_replace('<\/', '</', $_script);
            }
            $scripts[] = $_script;
            // $scripts[] = $this->getSaveHTML($tempDom);

            $node->parentNode->removeChild($node);
            $j++;
        }

        // Remove all script from raw HTML
        $html = str_replace($scripts, '', $html);
        // remove twice because saved php html !== real html markup
        $regExp = '/<script\b[^>]*>.*?<\/script>/is';
        $matches = array();
        preg_match_all($regExp, $html, $matches);
        $allScripts = [];
        if ($i === 0) {
            $allScripts = $matches[0];
        } else {
            foreach ($matches[0] as $mscript) {
                if ($i !== 0 && strpos($mscript, $ignorePlaceholder) !== false) {
                    continue;
                }
                $allScripts[] = $mscript;
            }
        }
        $html = str_replace($allScripts, '', $html);

        // $html = $this->getSaveHTML($dom);
        $scripts = implode("", $scripts);
        //console.log('some ø, æ, å characters')
        // $scripts = $this->getUtf8ToHtml($scripts);

        if ($isUnpackScript) {
            $unpackScript = $this->getUnpackScript();
            $scripts .= $unpackScript;
        }

        $html = str_replace('</body>', $scripts . '</body>', $html);
        $response->setBody($html);

        if ($this->config->isHTTP2ServerPushForJsEnabled()) {
            $this->HTTP2ServerPush($response, $hrefSrc, 'script', 'preload');
        }

        return $response;
    }
}
