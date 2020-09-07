<?php
namespace Swissup\Askit\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\Exception\LocalizedException;
use Swissup\Askit\Api\Data\MessageInterface;
use Swissup\Askit\Model\Message;

class Save extends \Magento\Backend\App\Action
{

    /**
     *
     * @var \Swissup\Askit\Model\MessageFactory
     */
    protected $messageFactory;


    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Swissup\Askit\Model\MessageFactory $messageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Swissup\Askit\Model\MessageFactory $messageFactory
    ) {
        parent::__construct($context);

        $this->backendAuthSession = $backendAuthSession;
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
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->messageFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->addData($data);

            try {
                $model->save();

                $this->_eventManager->dispatch(
                    'askit_message_after_save',
                    ['message' => $model, 'request' => $this->getRequest()]
                );

                $this->messageManager->addSuccess(__('You question saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if (!empty($data['answer'])) {
                    $answer = $this->messageFactory->create();
                    $adminUser = $this->backendAuthSession->getUser();

                    $answer
                        ->setParentId($model->getId())
                        ->setStatus(Message::STATUS_APPROVED)
                        ->setStoreId($model->getStoreId())
                        ->setText($data['answer'])
                        ->setItemTypeId($model->getItemTypeId())
                        ->setItemId($model->getItemId())
                        ->setHint(0)
                        // ->setCustomerName('admin')
                        ->setCustomerName($adminUser->getFirstname() . ' ' . $adminUser->getLastname())
                        ->setEmail($adminUser->getEmail())
                        ->save();
                    $this->_eventManager->dispatch(
                        'askit_add_new_answer',
                        ['message' => $model, 'request' => $this->getRequest()]
                    );

                    $model->setStatus(Message::STATUS_APPROVED);
                    $model->save();

                    $this->messageManager->addSuccess(__('You answer was added.'));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the question.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $this->getRequest()->getParam('id')]
            );
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
