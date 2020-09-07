<?php

namespace Swissup\Askit\Block\Adminhtml\Question\Edit\Tab\Answers\Grid\Renderer;

use Swissup\Askit\Api\Data\MessageInterface;
use Swissup\Askit\Model\Message;
use Swissup\Askit\Model\Message\Status as MessageStatus;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var array
     */
    protected static $statuses;

    /**
     * Constructor for Grid Renderer Status
     *
     * @return void
     */
    protected function _construct()
    {
        self::$statuses = MessageStatus::getOptionArray();
        parent::_construct();
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $class = '';
        $value = __($this->getStatus($row->getStatus()));

        switch ($row->getStatus()) {
            case MessageInterface::STATUS_DISAPPROVED:
                $class = 'critical';
                break;
            case MessageInterface::STATUS_APPROVED:
                $class = 'notice';
                break;
            case MessageInterface::STATUS_DISAPPROVED:
                $class = 'minor';
                break;
            case MessageInterface::STATUS_PENDING:
            case MessageInterface::STATUS_CLOSE:
            default:
                $class = 'minor';
                break;
        }
        return '<span class="grid-severity-' . $class . '">' .
            '<span>' . $value . '</span>' .
        '</span>';
    }

    /**
     * @param string $status
     * @return \Magento\Framework\Phrase
     */
    public static function getStatus($status)
    {
        if (isset(self::$statuses[$status])) {
            return self::$statuses[$status];
        }

        return __('Unknown');
    }
}
