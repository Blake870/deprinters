<?php
namespace Swissup\Askit\Controller\Adminhtml\Answer;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Swissup_Askit::message_save');
    }

    /**
     * Edit Askit item
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            // $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Swissup\Askit\Model\Message');
                $model->load($id);
                $questionId = $model->getParentId();

                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The answer has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('askit/question/edit', ['id' => $questionId]);
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a answer to delete.'));
        // go to grid
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
