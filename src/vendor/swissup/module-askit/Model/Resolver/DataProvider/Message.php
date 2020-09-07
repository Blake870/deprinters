<?php
declare(strict_types=1);

namespace Swissup\Askit\Model\Resolver\DataProvider;

use Swissup\Askit\Api\Data\MessageInterface;
use Magento\Store\Model\Store;
use Swissup\Askit\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Model\Template\FilterEmulate;

/**
 * Askit message data provider
 */
class Message
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FilterEmulate
     */
    private $widgetFilter;

    /**
     * @param CollectionFactory $collectionFactory,
     * @param FilterEmulate $widgetFilter
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        FilterEmulate $widgetFilter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->widgetFilter = $widgetFilter;
    }

    /**
     * @param array $stores
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData($stores): array
    {
        $stores[] = Store::DEFAULT_STORE_ID;
        $collection = $this->collectionFactory->create()
            ->addStatusFilter(MessageInterface::STATUS_APPROVED)
            ->addStoreFilter($stores)
            ->addQuestionFilter(0)
            ->addPrivateFilter()
            ;

        $data = [
            'size' => $collection->getSize(),
        ];
        $questions = [];
        foreach ($collection as $message) {
            $dataMessage = $this->getDataArray($message);

            $answers = [];
            if ($message->getParentId() == 0) {
                $answerCollection = $message->getApprovedAnswerCollection();
                foreach ($answerCollection as $answerMessage) {
                    $answers[] = $this->getDataArray($answerMessage);
                }
            }
            $dataMessage['answers'] = $answers;
            $questions[$message->getId()] = $dataMessage;
        }
        $data['questions'] = $questions;

        return $data;
    }

    /**
     *
     * @param  MessageInterface $message
     * @return array
     */
    protected function getDataArray(MessageInterface $message): array
    {
        $text = $this->widgetFilter->filter(
            $message->getText()
        );

        $data = [
            MessageInterface::ID            => $message->getId(),
            MessageInterface::PARENT_ID     => $message->getParentId(),
            MessageInterface::STORE_ID      => $message->getStoreId(),
            MessageInterface::CUSTOMER_ID   => $message->getCustomerId(),
            MessageInterface::CUSTOMER_NAME => $message->getCustomerName(),
            MessageInterface::EMAIL         => $message->getEmail(),
            MessageInterface::TEXT          => $text,
            MessageInterface::HINT          => $message->getHint(),
            MessageInterface::STATUS        => $message->getStatus(),
            MessageInterface::CREATED_TIME  => $message->getCreatedTime(),
            MessageInterface::UPDATE_TIME   => $message->getUpdateTime(),
            MessageInterface::IS_PRIVATE    => $message->getIsPrivate(),
        ];

        return $data;
    }
}
