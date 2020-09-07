<?php

namespace Swissup\Pagespeed\Block\Html\Header;

use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;

class CriticalCssFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = \Magento\Theme\Block\Html\Header\CriticalCss::class;

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @return bool
     */
    public function isExist()
    {
        return class_exists(self::CLASS_NAME, false);
    }

    /**
     *
     * @return CriticalCss
     */
    public function create()
    {
        return $this->objectManager->create(self::CLASS_NAME);
    }
}
