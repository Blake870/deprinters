<?php
/**
 * Plugin for Magento\Checkout\Block\Cart\Sidebar
 */
namespace Swissup\Ajaxpro\Plugin\Block\Cart;

class SidebarPlugin
{
    /**
     *
     * @var \Swissup\Ajaxpro\Helper\Config
     */
    private $helper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @param \Swissup\Ajaxpro\Helper\Config $helper
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Swissup\Ajaxpro\Helper\Config $helper,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->helper = $helper;
        $this->serializer = $serializer;
    }
    /**
     * Replace js component class
     *
     * @param  \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param  string                               $result
     * @return string
     */
    public function afterGetJsLayout(
        \Magento\Checkout\Block\Cart\Sidebar $subject,
        $result
    ) {
        json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $result;
        }
        $jsLayout = $this->serializer->unserialize($result, true);
        $isOverrideMinicart = $this->helper->isOverrideMinicart();
        if (isset($jsLayout['components']['minicart_content']['component'])
            && $isOverrideMinicart
        ) {
            $jsLayout['components']['minicart_content']['component'] = 'Swissup_Ajaxpro/js/view/minicart';
            $result = $this->serializer->serialize($jsLayout);
        }
        return $result;
    }
}
