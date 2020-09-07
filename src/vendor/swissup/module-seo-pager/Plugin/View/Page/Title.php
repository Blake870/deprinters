<?php

namespace Swissup\SeoPager\Plugin\View\Page;

use Swissup\SeoPager\Model\Filter\Title as TitleFilter;

class Title
{
    /**
     * @var \Swissup\SeoPager\Helper\Data
     */
    private $helper;

    /**
     * @var TitleFilter
     */
    private $titleFilter;

    /**
     * @param \Swissup\SeoPager\Helper\Data $helper
     */
    public function __construct(
        \Swissup\SeoPager\Helper\Data $helper,
        TitleFilter $titleFilter
    ) {
        $this->helper = $helper;
        $this->titleFilter = $titleFilter;
    }

    /**
     * Plugin `after` for \Magento\Framework\View\Page\Title::get()
     *
     * @param  \Magento\Framework\View\Page\Title $subject
     * @param  string                             $result
     * @return string
     */
    public function afterGet(
        \Magento\Framework\View\Page\Title $subject,
        $result
    ) {
        if ($this->helper->isEnabled()
            && $this->titleFilter->currentPageDirective() > 1
        ) {
            $object = new \Magento\Framework\DataObject(['title' => $result]);
            $result = $this->titleFilter->setScope($object)->filter(
                $this->helper->getTitleTemplate()
            );
        }

        return $result;
    }
}
