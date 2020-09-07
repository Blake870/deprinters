<?php

namespace Swissup\Hreflang\Model\CurrentUrl;

use Swissup\SeoCore\Model\CurrentUrl\ProviderInterface;

class HighlightViewIndex implements ProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * __construct
     *
     * @param \Magento\Framework\App\RequestInterface   $request       [description]
     * @param \Magento\Framework\ObjectManagerInterface $objectManager [description]
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->request = $request;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(
        \Magento\Store\Model\Store $store,
        $queryParamsToUnset = []
    ) {
        $pathInfo = $this->request->getAlias(
            \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS
        );
        $pageHelper = $this->objectManager->get('\Swissup\Highlight\Helper\Page');
        $type = $pageHelper->getPageTypeByUrlKey($pathInfo);
        if (!$type) {
            return null;
        }

        $newPathInfo = $store->getConfig("highlight/{$type}/url");
        $url = $store->getCurrentUrl(false, $queryParamsToUnset);
        return $pathInfo == $newPathInfo
            ? $url
            : str_replace($pathInfo, $newPathInfo, $url);
    }
}
