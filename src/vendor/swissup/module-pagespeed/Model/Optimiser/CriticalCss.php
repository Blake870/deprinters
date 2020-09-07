<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class CriticalCss extends AbstractOptimiser
{
    /**
     *
     * @var \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory
     */
    private $criticalCssViewModelFactory;

    /**
     * @param Config $config
     * @param \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory $criticalCssViewModelFactory
     */
    public function __construct(
        Config $config,
        \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory $criticalCssViewModelFactory
    ) {
        parent::__construct($config);
        $this->criticalCssViewModelFactory = $criticalCssViewModelFactory;
    }

    /**
     *
     * @return string
     */
    protected function getCriticalCss()
    {
        if ($this->config->isUseCssCriticalPathEnable()
         && $this->criticalCssViewModelFactory->isExist()
        ) {
            $styles = $this->criticalCssViewModelFactory->create()->getCriticalCssData();
        } else {
            $styles = $this->config->getDefaultCriticalCss();
        }

        return $styles;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isCriticalCssEnable()
            || $response === null
        ) {
            return $response;
        }

        $html = $response->getBody();
        $styles = $this->getCriticalCss();

        if (!empty($styles)) {
            $needle = '</title>';
            $pos = strpos($html, $needle);
            if ($pos !== false) {
                $html = str_replace($styles, '', $html);
                $html = str_replace('<style type="text/css" data-type="criticalCss"></style>', '', $html);
                if (!empty($styles)) {
                    $styles = '<style type="text/css" data-type="criticalCss">' . "\n"
                         . '    ' . $styles . "\n"
                        . '</style>';
                }
                $html = substr_replace($html, $needle . $styles, $pos, strlen($needle));
                $response->setBody($html);
            }
        }

        return $response;
    }
}
