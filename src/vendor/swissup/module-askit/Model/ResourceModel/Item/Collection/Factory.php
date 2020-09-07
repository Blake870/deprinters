<?php
namespace Swissup\Askit\Model\ResourceModel\Item\Collection;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Answer\Collection
     */
    public function create()
    {
        return $this->objectManager->create(\Swissup\Askit\Model\ResourceModel\Item\Collection::class);
    }
}
