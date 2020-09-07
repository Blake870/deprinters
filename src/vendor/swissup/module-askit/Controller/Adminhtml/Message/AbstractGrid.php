<?php
namespace Swissup\Askit\Controller\Adminhtml\Message;

use Magento\Backend\App\Action;

use Magento\Framework\Controller\ResultFactory;

use Swissup\Askit\Api\Data\MessageInterface;

abstract class AbstractGrid extends Action
{
    /**
     * @var string
     */
    protected $gridBlockName = '';

    /**
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('item_type_id', false);

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $grid = $resultLayout->getLayout()->getBlock($this->gridBlockName);
        $grid->setUseAjax(true);
        switch ($type) {
            case 'customer':
                $customerId = $this->getRequest()->getParam('customer_id', false);
                $grid->setCustomerId($customerId);
                break;
            case MessageInterface::TYPE_CMS_PAGE:
                $pageId = $this->getRequest()->getParam('page_id', false);
                $grid->setPageId($pageId);
                break;
            case MessageInterface::TYPE_CATALOG_CATEGORY:
                $categoryId = $this->getRequest()->getParam('id', false);
                $grid->setCategoryId($categoryId);
                break;
            case MessageInterface::TYPE_CATALOG_PRODUCT:
            default:
                $productId = $this->getRequest()->getParam('id', false);
                $grid->setProductId($productId);
                break;
        }
        return $resultLayout;
    }
}
