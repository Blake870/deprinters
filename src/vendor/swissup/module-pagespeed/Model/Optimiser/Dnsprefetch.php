<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class Dnsprefetch extends AbstractOptimiser
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        parent::__construct($config);
        $this->storeManager = $storeManager;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDnsPrefetchEnable() || $response === null) {
            return $response;
        }

        $html = $response->getBody();
        if (empty($html)) {
            return $response;
        }
        $xpath = $this->getDOMXPath($html);
        $templates = array(
            '//link'   => 'href',
            '//script' => 'src',
            '//img'    => 'src',
        );
        $urls = [];
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $baseUrl = parse_url($baseUrl, PHP_URL_HOST);

        foreach ($templates as $xpathString => $attribute) {
            $nodes = $xpath->query($xpathString);
            foreach ($nodes as $node) {
                $url = $node->getAttribute($attribute);
                $_url = parse_url($url, PHP_URL_HOST);
                if (!empty($_url) && false === strpos($url, $baseUrl)) {
                    $urls['//' . $_url] = $url;
                }
            }
        }

        if (!empty($urls)) {
            $_html = '';
            // $_html = '<meta http-equiv="x-dns-prefetch-control" content="on">' . "\n";
            foreach (array_keys($urls) as $url) {
                $_html .= "<link rel=\"dns-prefetch\" href=\"{$url}\">\n";
            }

            $needle = '</title>';
            $pos = strpos($html, $needle);
            if ($pos !== false) {
                $html = substr_replace($html, $needle . $_html, $pos, strlen($needle));
                $response->setBody($html);
                $response->setHeader('X-DNS-Prefetch-Control', 'on');
            }
        }

        return $response;
    }
}
