<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Edit\Tab;

class NewAnswer extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    *
    * @return \Swissup\Askit\Model\Message
    */
    protected function _getModel()
    {
        return $this->_coreRegistry->registry('askit_question');
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    // protected function _prepareForm()
    public function initForm()
    {
        /* @var $model \Swissup\Askit\Model\Message */
        $model = $this->_getModel();//$this->_coreRegistry->registry('askit_question');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Swissup_Askit::message_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
            'data' => [
                'id' => 'new_answer_tab_edit_form',
                'action' => $this->getUrl('*/answer/save'),
                'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('answer_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Add New Answer'), 'class' => 'fieldset-wide']
        );

        $isNew = !$model->getId();
        // if ($isNew) {
        //     return parent::_prepareForm();
        // }
        // if (!$isNew) {
        //     $fieldset->addField('id', 'hidden', ['name' => 'id']);
        //     $fieldset->addField('parent_id', 'hidden', ['name' => 'parent_id']);
        //     $fieldset->addField('is_answer', 'hidden', ['name' => 'parent_id']);
        // }

        $wysiwygConfig = $this->wysiwygConfig->getConfig(['hidden' => true]);

        $field = $fieldset->addField(
            'answer',
            'editor',
            [
                'name' => 'answer',
                'label' => __('Text'),
                'title' => __('Text'),
                'style' => 'height:36em',
                // 'required' => true,
                'disabled' => $isElementDisabled,
                'state' => 'html',
                'config' => $wysiwygConfig
            ]
        );
        $field->setAfterElementHtml(
            '<script>' .
                "require(['jquery'], function($){" .
                "   setTimeout(function(){
                        if (wysiwyganswer_answer && wysiwyganswer_answer.wysiwygInstance && wysiwyganswer_answer.wysiwygInstance.turnOn)
                        {
                            // wysiwyganswer_answer.wysiwygInstance.turnOn();
                            wysiwyganswer_answer.wysiwygInstance.turnOff();
                        }
                    }, 300);" .
                '});' .
            '</script>'
        );

        // $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_main_prepare_form', ['form' => $form]);

        // $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
        // return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Add New Answer');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Add New Answer');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $model = $this->_getModel();
        return (bool) $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->canShowTab();
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

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
