<?php
namespace Swissup\Askit\Block\Adminhtml\Answer;

class Edit extends \Swissup\Askit\Block\Adminhtml\Message\AbstractEdit
{
    /**
     *
     * @var string
     */
    protected $registryKey = 'askit_answer';

    /**
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Swissup_Askit';
        $this->_controller = 'adminhtml_answer';
        parent::_construct();
    }


    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $model = $this->_getModel();
        if ($model->getId()) {
            return __("Edit '%1'", $this->escapeHtml($model->getText()));
        }
        return __('New');
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
