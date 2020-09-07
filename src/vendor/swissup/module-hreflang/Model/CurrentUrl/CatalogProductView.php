<?php

namespace Swissup\Hreflang\Model\CurrentUrl;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Swissup\SeoCore\Model\CurrentUrl\ProviderInterface;

class CatalogProductView implements ProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * __construct
     *
     * @param \Magento\Framework\App\RequestInterface      $request
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->urlFinder = $urlFinder;
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
        $product = $this->registry->registry('current_product');
        $rewrite = $this->findRewrite('product', $product->getId(), $store->getId());
        if (!$rewrite) {
            return null;
        }

        $url = $store->getCurrentUrl(false, $queryParamsToUnset);
        return $pathInfo == $rewrite->getRequestPath()
            ? $url
            : str_replace($pathInfo, $rewrite->getRequestPath(), $url);
    }

    /**
     * Find url rewrite
     *
     * @param  string     $entityType
     * @param  int        $entityId
     * @param  int        $storeId
     * @return UrlRewrite
     */
    protected function findRewrite($entityType, $entityId, $storeId)
    {
        return $this->urlFinder->findOneByData(
                [
                    UrlRewrite::ENTITY_TYPE => $entityType,
                    UrlRewrite::ENTITY_ID => $entityId,
                    UrlRewrite::STORE_ID => $storeId,
                    UrlRewrite::REDIRECT_TYPE => 0,
                ]
            );
    }
}
