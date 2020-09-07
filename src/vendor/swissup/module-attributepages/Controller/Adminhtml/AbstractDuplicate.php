<?php
namespace Swissup\Attributepages\Controller\Adminhtml;

class AbstractDuplicate extends \Magento\Backend\App\Action
{
    /**
     * @var \Swissup\Attributepages\Model\Entity\Copier
     */
    protected $entityCopier;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swissup\Attributepages\Model\Entity\Copier $entityCopier
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Attributepages\Model\Entity\Copier $entityCopier
    ) {
        $this->entityCopier = $entityCopier;
        parent::__construct($context);
    }
    /**
     * Duplicate action
     *
     * @return void
     */
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('entity_id')) {
            try {
                $model = $this->_objectManager->create('Swissup\Attributepages\Model\Entity');
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This page no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
                $newModel = $this->entityCopier->copy($model);
                $this->messageManager->addSuccess(__('The page has been duplicated.'));
                $this->_redirect('*/*/edit', ['_current' => true, 'entity_id' => $newModel->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['entity_id' => $id]);
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find a page to duplicate.'));
        $this->_redirect('*/*/');
    }
}
