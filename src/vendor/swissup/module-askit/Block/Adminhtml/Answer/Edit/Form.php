<?php
namespace Swissup\Askit\Block\Adminhtml\Answer\Edit;

/**
 * Adminhtml blog post edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('answer_form');
        $this->setTitle(__('Answer Information'));
    }


   /**
    *
    * @return \Swissup\Askit\Model\Message
    */
    protected function _getModel()
    {
        return $this->_coreRegistry->registry('askit_answer');
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Swissup\Askit\Model\Message $model */
        $model = $this->_getModel();
        $isNew = null == $model->getId();
        $isElementDisabled = !$this->_isAllowedAction('Swissup_Askit::message_save');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('answer_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        // $fieldset = $form->addFieldset(
        //     'base_fieldset',
        //     ['legend' => __('Information'), 'class' => 'fieldset-wide']
        // );
        if (!$isNew) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('parent_id', 'hidden', ['name' => 'parent_id']);


        $wysiwygConfig = $this->wysiwygConfig->getConfig([
            'tab_id' => $this->getTabId(),
            'hidden' => true
        ]);

        $contentField = $fieldset->addField(
            'text',
            'editor',
            [
                'name' => 'text',
                'label' => __('Text'),
                'title' => __('Text'),
                'style' => 'height:36em',
                'required' => true,
                'disabled' => $isElementDisabled,
                'state' => 'html',
                'config' => $wysiwygConfig
            ]
        );

        $fieldset->addField(
            'hint',
            'text',
            [
                'name' => 'hint',
                'label' => __('Votes'),
                'title' => __('Votes'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'customer_name',
            $isNew ? 'text' : 'label',
            [
              'name'      => 'customer_name',
              'label'     => __('Customer'),
              'title'     => __('Customer'),
              'required'  => true,
              'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);

        // $fieldset->addField('item_type_id', 'hidden', ['name' => 'item_type_id']);

        $fieldset->addField(
            'email',
            $isNew ? 'text' : 'label',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getAnswerStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField(
        //     'is_private',
        //     'select',
        //     [
        //         'label' => __('Private'),
        //         'title' => __('Private'),
        //         'name' => 'is_private',
        //         'required' => true,
        //         'options' => $model->getPrivateStatuses(),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
