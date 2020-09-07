<?php

namespace Swissup\Easybanner\Plugin\Model;

class LayoutUpdate
{
    const REGISTRY_KEY = 'swissup_easybanner_layout_applied';

    /**
     * @var \Swissup\Easybanner\Model\Layout\Builder
     */
    private $layoutBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param \Swissup\Easybanner\Model\Layout\Builder $layoutBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Swissup\Easybanner\Model\Layout\Builder $layoutBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->layoutBuilder = $layoutBuilder;
        $this->registry = $registry;
    }

    /**
     * Generate layout updates with placeholders
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param $result
     * @param $handle
     */
    public function afterGetDbUpdateString(
        \Magento\Framework\View\Model\Layout\Merge $subject,
        $result,
        $handle = null // Compatibility with 2.1
    ) {
        // does not work with Magento 2.1
        // if ($handle !== 'default') {
        //     return;
        // }

        if ($this->registry->registry(self::REGISTRY_KEY)) {
            return $result;
        }

        $this->registry->register(self::REGISTRY_KEY, true);

        return $result . $this->layoutBuilder->generateLayoutUpdate();
    }
}
