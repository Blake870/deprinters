<?php
namespace Swissup\Askit\Controller\Adminhtml\Message;

use Swissup\Askit\Api\Data\MessageInterface;
use Swissup\Askit\Controller\Adminhtml\AbstractMassStatus;
use Magento\Framework\Controller\ResultFactory;
use Swissup\Askit\Model\ResourceModel\Message\Collection as AbstractCollection;

/**
 * Class MassStatus
 */
class MassStatus extends AbstractMassStatus//\Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'main_table.id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collectionClass = 'Swissup\Askit\Model\ResourceModel\Message\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $modelClass = 'Swissup\Askit\Model\Message';

    /**
     * item status
     * @var int
     */
    protected $status = MessageInterface::STATUS_PENDING;

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        $this->status = (int) $this->getRequest()->getParam('change_status', false);
        try {
            if (isset($excluded)) {
                if (!empty($excluded) && 'false' != $excluded) {
                    $this->excludedChange($excluded);
                } else {
                    $this->changeAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedChange($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     *
     * @return void
     * @throws \Exception
     */
    protected function changeAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->getCollection();
        $this->setSuccessMessage($this->change($collection));
    }

    /**
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedChange(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setSuccessMessage($this->change($collection));
    }

    /**
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedChange(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setSuccessMessage($this->change($collection));
    }

    /**
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function change(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->modelClass);
            $model->load($id);
            $model->setStatus($this->status);
            $model->save();
            ++$count;
        }

        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been changed.', $count));
    }
}
