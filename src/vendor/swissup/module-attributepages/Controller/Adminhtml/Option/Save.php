<?php
namespace Swissup\Attributepages\Controller\Adminhtml\Option;

use Swissup\Attributepages\Model\Entity as AttributepagesEntity;

class Save extends \Swissup\Attributepages\Controller\Adminhtml\AbstractSave
{
    const ADMIN_RESOURCE = 'Swissup_Attributepages::option_save';

    /**
     * Save action
     */
    public function execute()
    {
         if (!$data = $this->getRequest()->getPost('attributepage')) {
            $this->_redirect('*/*/');
            return;
        }
        $model = $this->_objectManager->create('Swissup\Attributepages\Model\Entity');
        if ($id = $this->getRequest()->getParam('entity_id')) {
            $model->load($id);
        }

        if (!$this->_validatePostData($data)) {
            $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
            return;
        }
        try {
            $mediaPath = $this->getBaseDir(AttributepagesEntity::IMAGE_PATH);
            foreach (['image', 'thumbnail'] as $key) {
                try {
                    $imageName = $this->uploadModel
                        ->uploadFileAndGetName(
                            $key,
                            $mediaPath,
                            $data,
                            ['jpg','jpeg','gif','png', 'bmp']
                        );
                    $data[$key] = $imageName;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                if (isset($data[$key]) && is_array($data[$key])) {
                    if (!empty($data[$key]['delete'])) {
                        $this->ioFile->rm($mediaPath . $data[$key]['value']);
                        $data[$key] = null;
                    } else {
                        $data[$key] = $data[$key]['value'];
                    }
                }
            }

            $model->addData($data);
            $model->save();

            $this->messageManager->addSuccess(__('The page has been saved.'));
            $this->attrpageSession->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current'=>true]);
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->attrpageSession->setFormData($data);
        $this->_redirect('*/*/edit', ['_current'=>true]);
    }
}
