<?php
namespace Swissup\Askit\Model\ResourceModel;

use Magento\Framework\Api\Search\FilterGroup;
// use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Message repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MessageRepository implements \Swissup\Askit\Api\MessageRepositoryInterface
{
    /**
     * @var \Swissup\Askit\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Swissup\Askit\Api\Data\MessageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface|boolean
     */
    private $collectionProcessor = false;

    /**
     *
     * @param \Swissup\Askit\Api\Data\MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Swissup\Askit\Model\MessageFactory                          $messageFactory
     * @ param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Swissup\Askit\Api\Data\MessageSearchResultsInterfaceFactory $searchResultsFactory,
        \Swissup\Askit\Model\MessageFactory $messageFactory
        // CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->messageFactory = $messageFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        // $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
        $this->collectionProcessor = $this->getCollectionProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Swissup\Askit\Api\Data\MessageInterface $message)
    {
        throw new \Exception("@todo", 1);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($email, $websiteId = null)
    {
        throw new \Exception("@todo", 1);
        // $customerModel = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        // return $customerModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($messageId)
    {
        $messageModel = $this->messageFactory->create()->load($messageId);
        if (!$messageModel->getId()) {
            // message does not exist
            throw NoSuchEntityException::singleField('messageId', $messageId);
        }
        return $messageModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /** @var \Swissup\Askit\Model\ResourceModel\Message\Collection $collection */
        $collection = $this->messageFactory->create()->getCollection();

        if ($this->collectionProcessor) {
            $this->collectionProcessor->process($searchCriteria, $collection);
        } else {
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
        }

        $searchResults->setTotalCount($collection->getSize());

        $messages = [];
        /** @var \Swissup\Askit\Model\Message $messageModel */
        foreach ($collection as $messageModel) {
            $messages[] = $messageModel;
        }
        $searchResults->setItems($messages);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Swissup\Askit\Api\Data\MessageInterface $message)
    {
        return $this->deleteById($message->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($messageId)
    {
        $messageModel = $this->messageFactory->create()->load($messageId);
        $messageModel->delete();
        return true;
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated
     * @return CollectionProcessorInterface|boolean
     */
    private function getCollectionProcessor()
    {
        $class = 'Magento\Framework\Api\SearchCriteria\CollectionProcessor';
        if (!class_exists($class)) {
            return false;
        }
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get($class);
        }
        return $this->collectionProcessor;
    }

    /**
     * Add FilterGroup to the collection
     *
     * @param FilterGroup $filterGroup
     * @param AbstractDb $collection
     * @return void
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        AbstractDb $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
