<?php
namespace Swissup\Askit\Controller;

use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

use Swissup\Askit\Api\Data\MessageInterface;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder
    ) {
        $this->actionFactory = $actionFactory;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
    }

    /**
     * Validate and Match and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $prefix = 'questions';
        if (strpos($identifier, $prefix) !== 0) {
            return null;
        }
        $controller = $action = 'index';
        $parts = explode('/', $identifier);
        if (isset($parts[1]) && $parts[1] == 'customer') {
            $controller = 'customer';
        }

        $request->setModuleName('askit')
            ->setControllerName($controller)
            ->setActionName($action);

        $_identifier = str_replace($prefix . '/', '', $identifier);
        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->pageFactory->create();
        $storeId = $this->storeManager->getStore()->getId();
        $pageId = $page->checkIdentifier($_identifier, $storeId);
        if ($pageId) {
            $request->setParam('item_type_id', MessageInterface::TYPE_CMS_PAGE)
                ->setParam('page_id', $pageId);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        } else {
            $rewrite = $this->getRewrite($_identifier, $storeId);
            if ($rewrite) {
                $type = $rewrite->getEntityType();
                $id = $rewrite->getEntityId();
                if ('product' == $type) {
                    $request->setParam('item_type_id', MessageInterface::TYPE_CATALOG_PRODUCT);
                    $request->setParam('id', $id);
                } elseif ('category' == $type) {
                    $request->setParam('item_type_id', MessageInterface::TYPE_CATALOG_CATEGORY);
                    $request->setParam('id', $id);
                }
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            }
        }

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

    /**
     * @param string $requestPath
     * @param int $storeId
     * @return UrlRewrite|null
     */
    protected function getRewrite($requestPath, $storeId)
    {
        return $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => trim($requestPath, '/'),
            UrlRewrite::STORE_ID => $storeId,
        ]);
    }
}
