<?php
namespace Swissup\Askit\Model\ResourceModel\Answer\Grid;

use Magento\Framework\ObjectManagerInterface;

/**
 * Factory class for @see \Swissup\Askit\Model\ResourceModel\Answer\Grid\Collection
 */
class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(ObjectManagerInterface $objectManager, $instanceName = '\\Swissup\\Askit\\Model\\ResourceModel\\Answer\\Grid\\Collection')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Swissup\Askit\Model\ResourceModel\Answer\Grid\Collection
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
