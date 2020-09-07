<?php

namespace Swissup\Askit\Observer;

class SwissupAmpPageSupported implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $result = $observer->getResult();
        $page = $result->getCurrentPage();
        $supportedPages = $result->getSupportedPages();
        $page = implode('_', [
            $this->request->getModuleName(),
            $this->request->getControllerName(),
            $this->request->getActionName()
        ]);

        if (in_array($page, $supportedPages)) {
            $result->setIsPageSupported(true);
        }
    }
}
