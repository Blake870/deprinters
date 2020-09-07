<?php
namespace Swissup\Askit\Controller\Adminhtml\Answer;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Swissup\Askit\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @param Action\Context $context
     * @param \Swissup\Askit\Model\MessageFactory $messageFactory
     */
    public function __construct(
        Action\Context $context,
        \Swissup\Askit\Model\MessageFactory $messageFactory
    ) {
        parent::__construct($context);

        $this->messageFactory = $messageFactory;
    }

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
        $request = $this->getRequest();
        $data = $request->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->messageFactory->create();

            $id = $request->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->addData($data);

            try {
                $model->save();

                $this->_eventManager->dispatch(
                    'askit_message_after_save',
                    ['message' => $model, 'request' => $request]
                );
                $this->_eventManager->dispatch(
                    'askit_add_new_answer',
                    ['message' => $model, 'request' => $request]
                );

                $question = $this->messageFactory->create();
                $question = $question->load($data['parent_id']);

                if ($question->getId() && isset($data['status'])) {
                    $question->setStatus($data['status']);
                    $question->save();
                }

                $this->messageManager->addSuccess(__('You saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $model->getId(),
                        '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the question.')
                );
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $request->getParam('id')]);
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
