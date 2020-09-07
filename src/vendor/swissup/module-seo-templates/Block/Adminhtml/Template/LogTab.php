<?php

namespace Swissup\SeoTemplates\Block\Adminhtml\Template;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class LogTab extends TabWrapper
{
    /**
     * @var Registry
     */
    protected $registry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        $model = $this->registry->registry('seotemplates_template');
        return $model && $model->getId();
    }

    /**
     * Return Tab label
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Generation log');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/log', ['_current' => true]);
    }
}