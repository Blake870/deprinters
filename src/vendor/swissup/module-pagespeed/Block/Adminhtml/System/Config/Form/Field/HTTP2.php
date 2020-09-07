<?php

namespace Swissup\Pagespeed\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class HTTP2 extends Field
{
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;

    /**
     * GettingStarted constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->curlFactory = $curlFactory;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return array
     */
    private function check()
    {
        $url = $this->storeManager->getStore()->getBaseUrl();
        $url = str_replace('http://', 'https://', $url);
        $error = false;

        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

        $client = $this->curlFactory->create();
        $client->setOptions([
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0, // cURL will attempt to make an HTTP/2.0 request (can downgrade to HTTP/1.1)
        ]);
        $client->write(\Zend_Http_Client::GET, $url);
        $responseBody = $client->read();

        if (!empty($responseBody) && strpos($responseBody, 'HTTP/2') === 0) {
            $error = false;
            $message = "Server of the URL has HTTP/2 support.";
        } elseif ($client->getErrno()) {
            $error = true;
            $message = $client->getError();
        } else {
            $error = true;
            $message = "Server of the URL has no HTTP/2 support.";
        }
        $client->close();

        return ['message' => $message, 'error' => $error];
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $result = $this->check();
        $note = '';
        if (isset($result['error']) && $result['error'] === false) {
            $message = 'Supports HTTP/2';
            $note = $result['message'];
            $cssClass = 'message-success';
        } else {
            $message = 'Web server does not support HTTP/2';
            $note = $result['message'] . ' Check your server manually';
            $cssClass = 'message-error';
        }

        $apiUrl = 'https://tools.keycdn.com/http2-test';
        return '<a href="' . $apiUrl . '" class="message ' . $cssClass . '">' . $message . '</a>' .
        '<p class="message note"><span>' . $note . '</span></p>';
    }
}
