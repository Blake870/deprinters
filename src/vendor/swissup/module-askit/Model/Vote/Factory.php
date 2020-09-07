<?php
namespace Swissup\Askit\Model\Vote;

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
     * @return \Swissup\Askit\Model\Vote
     */
    public function create()
    {
        return $this->objectManager->create(\Swissup\Askit\Model\Vote::class);
    }
}
