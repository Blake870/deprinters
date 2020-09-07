<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class HTTP2ServerPush extends AbstractOptimiser
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
     * @param ResponseHttp $response
     * @param array|string $links
     * @param string       $as
     * @param string       $rel
     * @param string       $type
     * @return ResponseHttp
     */
    public function HTTP2ServerPush(ResponseHttp $response, $links, $as = 'style', $rel = 'preload', $type = '')
    {
        if ($this->config->isHTTP2ServerPushEnabled() === false) {
            return $response;
        }

        if (is_string($links)) {
            $links = [$links];
        }
        if (!empty($type)) {
            $type = '; type=' . $type;
        }

        $headerValue = [];
        $replaceHeader = true;
        //https://stackoverflow.com/questions/686217/maximum-on-http-header-values
        $headerValueLength = 0;
        $headerValueMaxLength = 1024 * 4;
        $header = $response->getHeader('Link');
        if ($header) {
            $headerValue[] = $header->getFieldValue();
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $baseUrl = parse_url($baseUrl, PHP_URL_HOST);

        foreach ($links as $link) {
            if (strpos($link, $baseUrl) === false) {
                continue;
            }
            $_headerValue = "<" . $link . ">; rel={$rel}; as={$as}" . $type;
            $headerValueLength += strlen($_headerValue) + 2;
            if ($headerValueLength > $headerValueMaxLength) {
                break;
            }
            $headerValue[] = $_headerValue;
        }
        $headerValue = implode(', ', $headerValue);
        $response->setHeader('Link', $headerValue, $replaceHeader);

        return $response;
    }
}
