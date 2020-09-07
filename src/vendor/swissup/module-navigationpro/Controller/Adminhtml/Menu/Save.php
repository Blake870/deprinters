<?php

namespace Swissup\Navigationpro\Controller\Adminhtml\Menu;

use Swissup\Navigationpro\Model\MenuFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Swissup_Navigationpro::menu_save';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        MenuFactory $menuFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->menuFactory = $menuFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('menu_id');

            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = 1;
            }
            if (empty($data['menu_id'])) {
                $data['menu_id'] = null;
            }
            if (empty($data['config_scopes'])) {
                $data['config_scopes'] = [];
            }

            /** @var \Swissup\Navigationpro\Model\Menu $model */
            $menu = $this->menuFactory->create()->load($id);
            if (!$menu->getId() && $id) {
                $this->messageManager->addError(__('This menu no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $menu->addData($data);

            try {
                $menu->save();
                $this->messageManager->addSuccess(__('You saved menu.'));
                $this->dataPersistor->clear('navigationpro_menu');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving menu.'));
            }

            $this->dataPersistor->set('navigationpro_menu', $data);

            if ($this->getRequest()->getParam('isAjax')) {
                $messages = array_map(function($message) {
                    return $message->getText();
                }, $this->messageManager->getMessages(true)->getItems());

                return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                    'messages' => $messages,
                    'error' => false,
                ]);
            } else {
                return $resultRedirect->setPath('*/*/edit', ['menu_id' => $menu->getId()]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
