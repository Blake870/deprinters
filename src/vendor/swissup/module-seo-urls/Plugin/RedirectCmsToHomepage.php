<?php
/**
 * Plugin for Magento\Cms\Controller\Page\View
 */
namespace Swissup\SeoUrls\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result;

class RedirectCmsToHomepage
{
    /**
     * @var \Swissup\SeoUrls\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Cms\Model\PageRepository
     */
    protected $cmsPageRepository;

    /**
     * @param \Swissup\SeoUrls\Helper\Data      $helper
     * @param Result\ForwardFactory             $resultForwardFactory
     * @param Result\RedirectFactory            $resultRedirectFactory
     * @param \Magento\Cms\Model\PageRepository $cmsPageRepository
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Data $helper,
        Result\ForwardFactory $resultForwardFactory,
        Result\RedirectFactory $resultRedirectFactory,
        \Magento\Cms\Model\PageRepository $cmsPageRepository
    ) {
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->cmsPageRepository = $cmsPageRepository;
    }

    /**
     * After pluging \Magento\Cms\Controller\Page\View::execute
     *
     * @param  \Magento\Cms\Controller\Page\View $subject
     * @param  \Closure $proceed
     * @return mixed
     */
    public function afterExecute(
        \Magento\Cms\Controller\Page\View $subject,
        $result
    ) {
        if ($this->helper->isHomepageRedirect()) {
            // redirect to homepage is enabled
            $request = $subject->getRequest();
            $pageId = $request->getParam('page_id', $request->getParam('id', false));
            try {
                $page = $this->cmsPageRepository->getById($pageId);
                if ($page->getIdentifier() == $this->helper->getHomepageIdentifier()) {
                    $redirect = $this->resultRedirectFactory->create();
                    $redirect->setUrl($this->helper->getHomepageUrl());
                    return $redirect;
                }
            } catch (NoSuchEntityException $e) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }
        }


        return $result;
    }
}
