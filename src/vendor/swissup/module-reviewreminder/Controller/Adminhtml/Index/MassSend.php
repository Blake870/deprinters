<?php
namespace Swissup\Reviewreminder\Controller\Adminhtml\Index;

use Swissup\Reviewreminder\Api\Data\EntityInterface;
use Magento\Framework\Controller\ResultFactory;
use Swissup\Reviewreminder\Model\ResourceModel\Entity\Collection as AbstractCollection;

/**
 * Class MassSend
 */
class MassSend extends MassActionAbstract
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Reviewreminder::send';

    /**
     * Reminder helper
     *
     * @var Swissup\Reviewreminder\Helper\Helper
     *
     */
    protected $helper;

    /**
     * @param Action\Context $context
     * @param \Swissup\Reviewreminder\Model\ResourceModel\Entity\CollectionFactory $collectionFactory
     * @param \Swissup\Reviewreminder\Helper\Helper $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Reviewreminder\Model\ResourceModel\Entity\CollectionFactory $collectionFactory,
        \Swissup\Reviewreminder\Helper\Helper $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context, $collectionFactory);
    }

    /**
     * @return int
     */
    protected function change()
    {
        $remindersIds = $this->collection->getAllIds();
        try {
            $this->helper->sendReminders($remindersIds);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while sending the reminder.'));
            return 0;
        }

        return count($remindersIds);
    }

    /**
     * Set success message
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 reminders have been sent.', $count));
    }
}
