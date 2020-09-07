<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     *
     * @var \Swissup\Askit\Helper\Url
     */
    protected $urlHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Swissup\Askit\Helper\Url $urlHelper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->urlHelper = $urlHelper;
        $urlBuilder = $context->getUrlBuilder();
        $this->urlHelper->setUrlBuilder($urlBuilder);

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
    protected function _prepareForm()
    {
        /* @var $model \Swissup\Askit\Model\Message */
        $model = $this->_getModel();

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
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('question_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Information'), 'class' => 'fieldset-wide']
        );

        $isNew = !$model->getId();
        if (!$isNew) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField('parent_id', 'hidden', ['name' => 'parent_id']);

        if ($isNew) {
            $model->setData('parent_id', '0');
            $model->setData('is_private', '1');
        }

        // $wysiwygConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $contentField = $fieldset->addField(
            'text',
            'textarea',
            [
                'name' => 'text',
                'label' => __('Text'),
                'title' => __('Text'),
                // 'style' => 'height:36em',
                'required' => true,
                'disabled' => $isElementDisabled,
                // 'config' => $wysiwygConfig
            ]
        );

        // $renderer = $this->getLayout()->createBlock(
        //     'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element'
        // )->setTemplate(
        //     'Magento_Cms::page/edit/form/renderer/content.phtml'
        // );
        // $contentField->setRenderer($renderer);

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getQuestionStatuses(),
                'disabled' => $isElementDisabled
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
            !$isNew ? 'label' : 'text',
            [
              'name'      => 'customer_name',
              'label'     => __('Customer'),
              'title'     => __('Customer'),
              'required'  => true,
              'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField('item_type_id',
        //     !$isNew ? 'label' : 'text',
        //     [
        //       'name'      => 'item_type_id',
        //       'label'     => __('Item Type'),
        //       'title'     => __('Item Type'),
        //       'required'  => true,
        //       'disabled' => $isElementDisabled
        //     ]
        // );
        // $fieldset->addField(
        //     'item_type_id',
        //     'select',
        //     [
        //         'label' => __('Item Type'),
        //         'title' => __('Item Type'),
        //         'name' => 'item_type_id',
        //         // 'required' => true,
        //         'options'  => $model->getEntityTypes(),
        //         'disabled' => !$isNew// $isElementDisabled
        //     ]
        // );

        // $fieldset->addField(
        //     'item_id',
        //     !$isNew ? 'hidden' : 'text',
        //     [
        //       'name'      => 'item_id',
        //       'label'     => __('Item'),
        //       'title'     => __('Item'),
        //       // 'required'  => true,
        //       'disabled'  => $isElementDisabled
        //     ]
        // );
        // if (!$isNew) {
        //     $a = $this->urlHelper->get($model->getItemTypeId(), $model->getItemId());
        //     $fieldset->addField(
        //         'item_name',
        //         'note',
        //         [
        //             'name'      => 'item_name',
        //             'label'     => __('Item'),
        //             'title'     => __('Item'),
        //             'text'      => '<a href="' . $a['href'] . '">' . $a['label'] . '</a>'
        //         ]
        //     );
        // }

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_private',
            'select',
            [
                'label' => __('Private'),
                'title' => __('Private'),
                'name' => 'is_private',
                'required' => true,
                'options' => $model->getPrivateStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        // $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
