<?php
namespace Swissup\Attributepages\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;
    /**
     * Page view helper
     *
     * @var \Swissup\Attributepages\Helper\Page\View
     */
    protected $pageViewHelper;
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Swissup\Attributepages\Helper\Page\View $pageViewHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Swissup\Attributepages\Helper\Page\View $pageViewHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->pageViewHelper = $pageViewHelper;
    }
    /**
     * Validate and Match Attribute Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');
        $pathParts = explode('/', $pathInfo);
        $identifiers = [];
        foreach ($pathParts as $i => $param) {
            $identifiers[] = urldecode($param);
            if ($i >= 1) {
                break;
            }
        }

        $page = $this->pageViewHelper->initPagesInRegistry(
            isset($identifiers[1]) ? $identifiers[1] : $identifiers[0], // current_page
            isset($identifiers[1]) ? $identifiers[0] : false,           // parent_page
            'identifier'
        );

        if (!$page) {
            return false;
        }

        $request->setRouteName('attributepages')
            ->setControllerName('page')
            ->setActionName('view')
            ->setParam('id', $page->getId());

        $parent = $this->pageViewHelper
            ->getRegistryObject('attributepages_parent_page');
        if ($parent) {
            $request->setParam('parent_id', $parent->getId());
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
}
