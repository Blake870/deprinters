<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Code\Minifier\HtmlFactory as MinifyHTMLFactory;
use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\Code\Minifier\Adapter\Css\CSSmin;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;

class Html extends AbstractOptimiser
{
    /**
     *
     * @var Swissup\Pagespeed\Code\Minifier\HtmlFactory
     */
    protected $minifierFactory;

    /**
     * @param Config $config
     * @param Swissup\Pagespeed\Code\Minifier\HtmlFactory $minifierFactory
     */
    public function __construct(Config $config, MinifyHTMLFactory $minifierFactory)
    {
        parent::__construct($config);
        $this->minifierFactory = $minifierFactory;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isContentMinifyEnable() || $response === null) {
            return $response;
        }
        $contentTypeHeader = $response->getHeader('content-type');
        if ($contentTypeHeader && $contentTypeHeader->getFieldValue() === 'text/plain') {
            return $response;
        }
        $html = $response->getBody();

        $options = [];
        $isCssMinifier = $this->config->isContentCssMinifyEnable();
        if ($isCssMinifier) {
            $options['cssMinifier'] = array('Minify_CSS', 'minify');
            // $options['cssMinifier'] = array('CSSmin', 'minify');
        }
        $isJsMinifier = $this->config->isContentJsMinifyEnable();
        if ($isJsMinifier) {
            $options['jsMinifier'] = array('JSMin\JSMin', 'minify');
            // $options['jsMinifier'] = array('JShrink\Minifier', 'minify');
        }

        $minifier = $this->minifierFactory->create([
            'html' => $html,
            'options' => $options
        ]);

        try {
            $_html = $minifier->process();
        } catch (\Exception $e) {
            // \Zend_Debug::dump($e->getMessage());
            // \Zend_Debug::dump($e->getTraceAsString());
            // die;
            throw $e;
        }

        $response->setBody($_html);

        return $response;
    }
}
