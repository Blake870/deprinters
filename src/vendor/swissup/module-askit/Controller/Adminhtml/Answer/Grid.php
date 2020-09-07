<?php
namespace Swissup\Askit\Controller\Adminhtml\Answer;

use Swissup\Askit\Controller\Adminhtml\Message\AbstractGrid as MessageGrid;
use Magento\Backend\App\Action\Context;

class Grid extends MessageGrid
{
    /**
     * @var string
     */
    protected $gridBlockName = 'askit_answer_listing';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
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

        return parent::execute();
    }
}
