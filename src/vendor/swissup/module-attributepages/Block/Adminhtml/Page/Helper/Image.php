<?php
namespace Swissup\Attributepages\Block\Adminhtml\Page\Helper;

use \Swissup\Attributepages\Model\Entity as AttributepagesEntity;
use \Magento\Framework\UrlInterface;

class Image extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->getBaseUrl()
                . AttributepagesEntity::IMAGE_PATH
                . $this->getValue();
        }
        return $url;
    }
    /**
     * Get images base url
     *
     * @return string
     */
    public function getBaseUrl($type = UrlInterface::URL_TYPE_MEDIA)
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => $type]);
    }
}
