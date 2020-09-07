<?php
namespace Swissup\Reviewreminder\Controller\Adminhtml\Index;

use Swissup\Reviewreminder\Api\Data\EntityInterface;
use Magento\Framework\Controller\ResultFactory;
use Swissup\Reviewreminder\Model\ResourceModel\Entity\Collection as AbstractCollection;

/**
 * Class MassStatus
 */
class MassActionAbstract extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'entity_id';

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * Resource collection
     *
     * @var \Swissup\Reviewreminder\Model\ResourceModel\Entity\Collection
     */
    protected $collection;

    /**
     * @param Action\Context $context
     * @param \Swissup\Reviewreminder\Model\ResourceModel\Entity\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Reviewreminder\Model\ResourceModel\Entity\CollectionFactory $collectionFactory
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        try {
            if (isset($excluded)) {
                if (!empty($excluded) && $excluded != "false") {
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

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     *
     * @return void
     * @throws \Exception
     */
    protected function changeAll()
    {
        $this->setSuccessMessage($this->change());
    }

    /**
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedChange(array $excluded)
    {
        $this->collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setSuccessMessage($this->change());
    }

    /**
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedChange(array $selected)
    {
        $this->collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setSuccessMessage($this->change());
    }
}
