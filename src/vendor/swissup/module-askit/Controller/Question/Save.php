<?php
namespace Swissup\Askit\Controller\Question;

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

        $isLoggedIn = $this->customerSession->isLoggedIn();
        $customer = $this->customerSession->getCustomer();
        $isAllowedGuestQuestion = $this->configHelper->isAllowedGuestQuestion();

        if (!$isLoggedIn && !$isAllowedGuestQuestion) {
            $this->messageManager->addError(__('Your must login'));
            return $this->redirectReferer();
        }

        try {
            $error = false;
            $errorMessage = '';

            if (!\Zend_Validate::is(trim($post['customer_name']), 'NotEmpty')) {
                $error = true;
                $errorMessage = 'Name can\'t be empty.';
            }
            if (!\Zend_Validate::is(trim($post['text']), 'NotEmpty')) {
                $error = true;
                $errorMessage = 'Question can\'t be empty.';
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
                $errorMessage = 'Email is not valid.';
            }

            if ($error) {
                throw new \Exception(__($errorMessage));
            }
            $post['customer_id'] = $isLoggedIn ? $customer->getId() : null;

            $post['store_id'] = $this->storeManager->getStore()->getId();

            $post['status'] = $this->configHelper->getDefaultQuestionStatus();

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
                $e->getMessage()  . ' '. __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
        }

        return $this->redirectReferer();
    }
}
