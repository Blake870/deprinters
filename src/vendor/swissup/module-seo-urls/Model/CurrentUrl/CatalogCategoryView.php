<?php

namespace Swissup\SeoUrls\Model\CurrentUrl;

use Magento\Framework\Session\SidResolverInterface;
use Magento\Store\Api\Data\StoreInterface;

class CatalogCategoryView extends AbstractProvider
{
    /**
     * @param \Swissup\SeoCore\Model\CurrentUrl\ProviderInterface $originalCurrentUrl
     * @param \Swissup\SeoUrls\Model\Url\Filter                   $seoUrlBuilder
     * @param \Magento\Store\Model\App\Emulation                  $emulation
     * @param \Magento\Framework\App\RequestInterface             $request
     */
    public function __construct(
        \Swissup\SeoCore\Model\CurrentUrl\ProviderInterface $originalCurrentUrl,
        \Swissup\SeoUrls\Model\Url\Filter $seoUrlBuilder,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->originalCurrentUrl = $originalCurrentUrl;
        parent::__construct($seoUrlBuilder, $emulation, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function provide(
        \Magento\Store\Model\Store $store,
        $queryParamsToUnset = []
    ) {
        if ($store->isUseStoreInUrl()) {
            // unset store param to get clean URL
            $queryParamsToUnset[] = '___store';
        }

        $seoHelper = $this->seoUrlBuilder->getData('seoHelper');
        if (!$seoHelper || !$seoHelper->isSeoUrlsEnabled()) {
            return $this->originalCurrentUrl->provide($store, $queryParamsToUnset);
        }

        // start store emulation to get correct url filters
        $this->emulation->startEnvironmentEmulation($store->getId());
        $appliedFilters = $this->getAppliedFilters();
        $this->emulation->stopEnvironmentEmulation();
        // stop store emulation

        if (empty($appliedFilters)) {
            return $this->originalCurrentUrl->provide($store, $queryParamsToUnset);
        }

        $rewrite = $this->originalCurrentUrl->getCategoryUrlRewrite($store);
        $url = $this->getUrlForStore($store, !!$rewrite, $queryParamsToUnset);
        if ($rewrite) {
            $pathInfo = $this->getRequestPathInfo();
            if ($pathInfo != $rewrite->getRequestPath()) {
                $url = str_replace($pathInfo, $rewrite->getRequestPath(), $url);
            }

            $url = $this->seoUrlBuilder->getData('seoUrl')
                ->rebuild($url, $appliedFilters);
        }

        return $url;

    }

    /**
     * Get URL
     *
     * @param  StoreInterface $store
     * @param  boolean        $useRewrite
     * @param  array          $queryParamsToUnset
     * @return string
     */
    protected function getUrlForStore(
        StoreInterface $store,
        $useRewrite = true,
        $queryParamsToUnset = []
    ) {
        $query = [];
        foreach ($queryParamsToUnset as $param) {
            $query[$param] = null;
        }

        if (!$store->isUseStoreInUrl()) {
            $query['___store'] = $store->getCode();
        }

        $sidParamName = SidResolverInterface::SESSION_ID_QUERY_PARAM;
        return $store->getUrl(
            '*/*/*',
            [
                '_current' => true,
                '_use_rewrite' => $useRewrite,
                '_nosid' => in_array($sidParamName, $queryParamsToUnset),
                '_query' => $query
            ]
        );
    }
}
