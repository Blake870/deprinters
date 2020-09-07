<?php

namespace Swissup\Hreflang\Model\CurrentUrl;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Store\Api\Data\StoreInterface;

class CatalogCategoryView extends CatalogProductView
{
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
        $rewrite = $this->getCategoryUrlRewrite($store);
        if (!$rewrite) {
            return null;
        }

        $url = $store->getCurrentUrl(false, $queryParamsToUnset);
        return $pathInfo == $rewrite->getRequestPath()
            ? $url
            : str_replace($pathInfo, $rewrite->getRequestPath(), $url);
    }

    /**
     * Get category rewrite for $store
     *
     * @param  StoreInterface $store
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    public function getCategoryUrlRewrite(StoreInterface $store)
    {
        $category = $this->registry->registry('current_category');
        return $this->findRewrite(
            'category',
            $category->getId(),
            $store->getId()
        );
    }
}
