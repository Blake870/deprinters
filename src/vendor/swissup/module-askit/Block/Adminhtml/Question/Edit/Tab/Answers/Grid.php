<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Edit\Tab\Answers;

use Swissup\Askit\Model\Message\Status;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Swissup\Askit\Model\ResourceModel\Answer\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Swissup\Askit\Model\ResourceModel\Answer\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Swissup\Askit\Model\ResourceModel\Answer\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('answersGrid');
        // $this->setDefaultSort('_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(__('No Found'));
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('askit/answer/grid', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $model = $this->_coreRegistry->registry('askit_question');
        /** @var $collection \Swissup\Askit\Model\ResourceModel\Answer\Collection */
        $collection = $this->_collectionFactory->create()
            ->addParentIdFilter($model->getId())
            ->addAnswerFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'answer_id', //phantom id
            ['header' => __('ID'), 'align' => 'left', 'index' => 'id', 'width' => 10]
        );

        $this->addColumn(
            'answer_text',
            [
                'header' => __('Text'),
                'type' => 'text',
                'align' => 'center',
                'index' => 'text',
                // 'default' => ' ---- '
            ]
        );

        $this->addColumn(
            'answer_customer_name',
            [
                'header' => __('Customer'),
                'type' => 'text',
                'align' => 'center',
                'index' => 'customer_name',
            ]
        );

        $this->addColumn(
            'answer_email',
            [
                'header' => __('Email'),
                'type' => 'text',
                'align' => 'center',
                'index' => 'email',
            ]
        );

        $this->addColumn(
            'answer_hint',
            [
                'header' => __('Votes'),
                'type' => 'text',
                'align' => 'center',
                'index' => 'hint',
            ]
        );

        $this->addColumn(
            'answer_status',
            [
                'header' => __('Status'),
                'align' => 'center',
                'filter' => Grid\Filter\Status::class,
                'index' => 'status',
                'renderer' => Grid\Renderer\Status::class
            ]
        );

        $this->addColumn(
            'answer_created_time',
            [
                'header' => __('Created date'),
                'type' => 'datetime',
                'align' => 'center',
                'index' => 'created_time',
                'default' => ' ---- '
            ]
        );

        $this->addColumn(
            'answer_update_time',
            [
                'header' => __('Update date'),
                'type' => 'datetime',
                'align' => 'center',
                'index' => 'update_time',
                'default' => ' ---- '
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'renderer' => Grid\Renderer\Action::class
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('selected');

        // $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('askit/message/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = Status::getOptionArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'change_status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('askit/message/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'change_status',
                        'type' => 'select',
                        // 'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('askit/answer/edit', ['id' => $row->getId()]);
    }
}
