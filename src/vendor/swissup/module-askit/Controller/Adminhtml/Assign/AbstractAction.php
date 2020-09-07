<?php
namespace Swissup\Askit\Controller\Adminhtml\Assign;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\LayoutFactory;

use Swissup\Askit\Api\Data\MessageInterface;

abstract class AbstractAction extends Action
{
    protected $listingBlockName = '';
    /**
     * @var LayoutFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param LayoutFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        LayoutFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Swissup\Askit\Model\Message');
        if ($id) {
            $model->load($id);
        }
        $this->coreRegistry->register('askit_question', $model);

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $grid = $resultLayout->getLayout()->getBlock($this->listingBlockName);
        $grid->setUseAjax(true);

        return $resultLayout;
    }
}
