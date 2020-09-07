<?php

namespace Swissup\Hreflang\Model\CurrentUrl;

use Swissup\SeoCore\Model\CurrentUrl\ProviderInterface;

class CmsPageView implements ProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPageInstance;

    /**
     * __construct
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Cms\Model\PageFactory          $cmsPageFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Cms\Model\PageFactory $cmsPageFactory
    ) {
        $this->request = $request;
        $this->cmsPageFactory = $cmsPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(
        \Magento\Store\Model\Store $store,
        $queryParamsToUnset = []
    ) {
        if (!isset($this->cmsPageInstance)) {
            $this->cmsPageInstance = $this->cmsPageFactory->create();
        }

        $identifier = $this->request->getAlias(
            \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS
        );
        $pageId = $this->cmsPageInstance->checkIdentifier(
            $identifier,
            $store->getId()
        );
        if (!$pageId) {
            // cms page with such identifier not found
            return null;
        }

        return $store->getCurrentUrl(false, $queryParamsToUnset);
    }
}
