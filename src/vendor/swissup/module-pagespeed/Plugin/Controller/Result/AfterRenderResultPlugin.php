<?php
namespace Swissup\Pagespeed\Plugin\Controller\Result;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Plugin for processing html
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AfterRenderResultPlugin
{
    /**
     * @var array
     */
    private $minifiers = [];

    /**
     * @param array $minifiers
     */
    public function __construct($minifiers = [])
    {
        $this->minifiers = $minifiers;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param ResponseHttp $response
     * @return ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRenderResult(ResultInterface $subject, ResultInterface $result, ResponseHttp $response)
    {
        if ($response === null) {
            return $result;
        }

        if (!$result instanceof \Magento\Framework\View\Result\Page) {
            return $result;
        }

        \Magento\Framework\Profiler::start(__METHOD__);
        foreach ($this->minifiers as $minifierFactoryClass) {
            $minifier = $minifierFactoryClass->create();
            $profilerTag = get_class($minifier) . '::process';
            \Magento\Framework\Profiler::start($profilerTag);
            $response = $minifier->process($response);
            \Magento\Framework\Profiler::stop($profilerTag);
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $result;
    }
}
