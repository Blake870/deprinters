<?php
namespace Swissup\Askit\Controller\Answer;

class Save extends \Swissup\Askit\Controller\Message\SaveAbstract
{
    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {

        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            return $this->redirectReferer();
        }

        try {
            $error = false;
            $errorMessage = '';

            if (!\Zend_Validate::is(trim($post['text']), 'NotEmpty')) {
                $error = true;
                $errorMessage = 'Answer can\'t be empty.';
            }

            if ($error) {
                throw new \Exception(__($errorMessage));
            }
            $isLoggedIn = $this->customerSession->isLoggedIn();
            $customer = $this->customerSession->getCustomer();

            $question = $this->messageFactory->create();
            $question->load($post['parent_id']);

            $post['item_id'] = $question->getItemId();
            $post['item_type_id'] = $question->getItemTypeId();
            $post['customer_id'] = $isLoggedIn ? $customer->getId() : null;

            $post['store_id'] = $this->storeManager->getStore()->getId();

            $post['status'] = $this->configHelper->getDefaultAnswerStatus();
            $post['hint'] = 0;
            if ($isLoggedIn) {
                $post['customer_name'] = $customer->getName();
                $post['email'] = $customer->getEmail();
            }

            $model = $this->messageFactory->create();

            $model->setData($post);

            $model->save();
            $this->_eventManager->dispatch(
                'askit_message_after_save',
                ['message' => $model, 'request' => $this->getRequest()]
            );

            $this->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
        } catch (\Exception $e) {
            // $this->inlineTranslation->resume();
            $this->messageManager->addError(
                $e->getMessage()  . ' ' . __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
        }
        return $this->redirectReferer();
    }
}
