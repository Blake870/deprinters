<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Edit\Tab\Answers\Grid\Renderer;

/**
 * Adminhtml newsletter queue grid block action item renderer
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $actions = [];

        $model = $this->_coreRegistry->registry('askit_question');

        $actions[] = [
            '@' => [
                'href' => $this->getUrl('askit/answer/edit', ['id' => $row->getId()]),
                'target' => '_blank',
            ],
            '#' => __('Edit'),
        ];

        $actions[] = [
            '@' => [
                'href' => $this->getUrl('askit/answer/delete', ['id' => $row->getId()]),
                'onClick' => sprintf("deleteConfirm('%s', this.href); return false;", __('Are you sure?'))
            ],
            '#' => __('Delete'),
        ];

        return $this->_actionsToHtml($actions);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
    }

    /**
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }
}
