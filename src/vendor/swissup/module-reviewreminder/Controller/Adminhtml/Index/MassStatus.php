<?php
namespace Swissup\Reviewreminder\Controller\Adminhtml\Index;

use Swissup\Reviewreminder\Api\Data\EntityInterface;
use Magento\Framework\Controller\ResultFactory;
use Swissup\Reviewreminder\Model\ResourceModel\Entity\Collection as AbstractCollection;

/**
 * Class MassStatus
 */
class MassStatus extends MassActionAbstract
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Reviewreminder::status';

    /**
     * Entity model factory
     *
     * @var Swissup\Reviewreminder\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * entity status
     * @var int
     */
    protected $status = \Swissup\Reviewreminder\Model\Entity::STATUS_PENDING;

    /**
     * @param Action\Context $context
     * @param \Swissup\Reviewreminder\Model\EntityFactory $entityFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Reviewreminder\Model\ResourceModel\Entity\CollectionFactory $collectionFactory,
        \Swissup\Reviewreminder\Model\EntityFactory $entityFactory
    ) {
        $this->entityFactory = $entityFactory;
        parent::__construct($context, $collectionFactory);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $this->status = (int) $this->getRequest()->getParam('status');

        return parent::execute();
    }

    /**
     * @return int
     */
    protected function change()
    {
        $count = 0;
        foreach ($this->collection->getAllIds() as $id) {
            $model = $this->entityFactory->create();
            $model->load($id);
            $model->setStatus($this->status);
            $model->save();
            ++$count;
        }

        return $count;
    }

    /**
     * Set success message
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been changed.', $count));
    }
}
