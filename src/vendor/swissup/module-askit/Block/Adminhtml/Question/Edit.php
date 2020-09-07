<?php
namespace Swissup\Askit\Block\Adminhtml\Question;

class Edit extends \Swissup\Askit\Block\Adminhtml\Message\AbstractEdit
{
    /**
     *
     * @var string
     */
    protected $registryKey = 'askit_question';

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Swissup_Askit';
        $this->_controller = 'adminhtml_question';

        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $model = $this->_getModel();
        if ($model->getId()) {
            return __("Edit Question '%1'", $this->escapeHtml(
                $model->getTitle()
            ));
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
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('question_text') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'question_text');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'question_text');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
