<?php
namespace Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab;

class Content
    extends \Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab\AbstractTab
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Swissup\Attributepages\Model\Entity */
        $model = $this->getPage();
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset',
            ['legend' => __('Content'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField('page_title', 'text', [
            'name'     => 'page_title',
            'label'    => __('Page Title'),
            'title'    => __('Page Title'),
            'disabled' => $isElementDisabled
        ]);
        $wysiwygConfig  = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $contentField = $fieldset->addField('content', 'editor', [
            'label'    => __('Description'),
            'title'    => __('Description'),
            'name'     => 'content',
            'style'    => 'height:15em;',
            'disabled' => $isElementDisabled,
            'config'   => $wysiwygConfig
        ]);
        $fieldset->addField('meta_keywords', 'textarea', [
            'name'     => 'meta_keywords',
            'label'    => __('Meta Keywords'),
            'title'    => __('Meta Keywords'),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('meta_description', 'textarea', [
            'name'     => 'meta_description',
            'label'    => __('Meta Description'),
            'title'    => __('Meta Description'),
            'disabled' => $isElementDisabled
        ]);

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('attributepage');
        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content');
    }
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Content');
    }
}
