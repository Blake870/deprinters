<?php
declare(strict_types=1);

namespace Swissup\Askit\Model\Resolver;

use Swissup\Askit\Model\Resolver\DataProvider\Message as DataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Data resolver, used for GraphQL request processing
 */
class Messages //implements ResolverInterface
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @param DataProvider $dataProvider
     */
    public function __construct(
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        $stores = $this->getStores($args);
        $resultData = $this->getMessagesData($stores);

        return $resultData;
    }

    /**
     * @param array $args
     * @return string[]
     * @throws GraphQlInputException
     */
    private function getStores(array $args): array
    {
        if (!isset($args['store']) || !is_array($args['store']) || count($args['store']) === 0) {
            throw new GraphQlInputException(__('"store" of askit messages should be specified'));
        }

        return $args['store'];
    }

    /**
     * @param array $stores
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getMessagesData(array $stores): array
    {
        $data = [];
        try {
            $data = $this->dataProvider->getData($stores);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $data;
    }
}
