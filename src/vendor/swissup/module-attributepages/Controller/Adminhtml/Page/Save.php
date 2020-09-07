<?php
namespace Swissup\Attributepages\Controller\Adminhtml\Page;

use Swissup\Attributepages\Model\Entity as AttributepagesEntity;

class Save extends \Swissup\Attributepages\Controller\Adminhtml\AbstractSave
{
    const ADMIN_RESOURCE = 'Swissup_Attributepages::page_save';

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
        $model->addData($data);
        if (!$this->_validatePostData($data)) {
            $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
            return;
        }
        try {
            $model->save();
            // save options
            $optionData = $this->getRequest()->getPost('option', []);
            $existingOptions = $this->_objectManager->create('Swissup\Attributepages\Model\Entity')
                ->getCollection()
                ->addOptionOnlyFilter()
                ->addFieldToFilter('attribute_id', $model->getAttributeId())
                ->addStoreFilter($this->storeManager->getStore());
            $optionToEntity = [];
            foreach ($existingOptions as $entity) {
                $optionToEntity[$entity->getOptionId()] = $entity->getId();
            }
            $messages  = [];
            $mediaPath = $this->getBaseDir(AttributepagesEntity::IMAGE_PATH);
            foreach ($model->getRelatedOptions() as $option) {
                $optionId = $option->getId();
                // skip if already exists and no changes are made
                if (isset($optionToEntity[$optionId]) && !isset($optionData[$optionId])) {
                    continue;
                }
                $_data = isset($optionData[$optionId]) ? $optionData[$optionId] : [];
                foreach (['image', 'thumbnail'] as $key) {
                    try {
                        $imageName = $this->uploadModel
                            ->uploadFileAndGetName(
                                'option_' . $optionId . '_' . $key,
                                $mediaPath,
                                $_data,
                                ['jpg','jpeg','gif','png', 'bmp']
                            );

                        if (false !== $imageName) {
                            $_data[$key] = $imageName;
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
                $entity = $this->_objectManager->create('Swissup\Attributepages\Model\Entity');
                if (!empty($_data['entity_id'])) {
                    $entity->load($_data['entity_id']);
                }
                unset($_data['entity_id']);
                if (!$entity->getId()) {
                    $entity->importOptionData($option);
                }
                $entity->addData($_data);
                try {
                    $urlChanged = false;
                    if (!$entity->getResource()->getIsUniquePageToStores($entity)) {
                        $urlChanged = true;
                        $entity->setIdentifier(
                            $entity->getIdentifier() . '-' . $option->getId()
                        );
                    }
                    $entity->save();
                    // show notice if url was changed
                    if ($urlChanged) {
                        $notice = __('The following urls where automatically changed because of duplicates.');
                        $messages['addNotice'][$notice->getText()][] = $entity->getIdentifier();
                    }
                } catch (\Exception $e) {
                    $messages['addError'][$e->getMessage()][] = $option->getValue();
                }
            }
            // display grouped error and notice messages
            foreach ($messages as $method => $groupedMessage) {
                foreach ($groupedMessage as $message => $ids) {
                    $this->messageManager->{$method}($message . '<br/>' . implode(', ', $ids));
                }
            }
            $this->messageManager->addSuccess(__('The page has been saved.'));
            $this->attrpageSession->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('entity_id' => $model->getId(), '_current'=>true));
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
