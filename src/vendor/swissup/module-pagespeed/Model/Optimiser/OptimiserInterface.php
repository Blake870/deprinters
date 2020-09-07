<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Magento\Framework\App\Response\Http as ResponseHttp;

interface OptimiserInterface
{
    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null);
}
