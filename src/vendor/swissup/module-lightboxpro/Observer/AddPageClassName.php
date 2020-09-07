<?php
namespace Swissup\Lightboxpro\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddPageClassName implements ObserverInterface
{
     /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Swissup\Lightboxpro\Helper\Config
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Swissup\Lightboxpro\Helper\Config $helper
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Swissup\Lightboxpro\Helper\Config $helper
    ) {
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->pageConfig->addBodyClass(
            'lightboxpro-' . $this->helper->getPopupLayoutType()
        );
    }
}
