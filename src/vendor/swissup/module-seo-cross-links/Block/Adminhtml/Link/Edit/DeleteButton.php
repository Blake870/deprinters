<?php

namespace Swissup\SeoCrossLinks\Block\Adminhtml\Link\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    protected $_template = 'Swissup_SeoCrossLinks::delete_button.phtml';

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getLinkId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['link_id' => $this->getLinkId()]);
    }
}
