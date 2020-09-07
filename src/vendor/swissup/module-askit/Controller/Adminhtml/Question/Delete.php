<?php
namespace Swissup\Askit\Controller\Adminhtml\Question;

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
     * Delete Askit item
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

                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The question has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'askit_message_prepare_on_delete',
                    ['message' => $model, 'status' => 'success']
                );
                return $resultRedirect->setPath('askit/question/index');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'askit_message_prepare_on_delete',
                    ['message' => $model, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a question to delete.'));
        // go to grid
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
