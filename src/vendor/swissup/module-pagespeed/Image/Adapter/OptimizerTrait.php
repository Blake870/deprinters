<?php
namespace Swissup\Pagespeed\Image\Adapter;

use Swissup\Pagespeed\Helper\Config;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Swissup\Pagespeed\Image\Optimizers\Cwebp;

trait OptimizerTrait
{
    use ConfigHelperTrait; // override __counsruct for di ConfigHelper

    /**
     *
     * @var OptimizerChain
     */
    protected $optimizerChain;

    /**
     *
     * @param  string $destination
     * @param  string $newName
     * @return string
     */
    abstract protected function _prepareDestination($destination = null, $newName = null);

    /**
     *
     * @param  string $filename
     * @return void
     */
    public function optimize($filename)
    {
        if ($this->configHelper->isImageOptimizerEnable()) {
            $this->getOptimizerChain()->optimize($filename);
        }
    }

    /**
     *
     * @return OptimizerChain
     */
    protected function getOptimizerChain()
    {
        if (!$this->optimizerChain) {
            $this->optimizerChain = OptimizerChainFactory::create();

            if ($this->configHelper->isWebPEnable()) {
                $this->optimizerChain->addOptimizer(new Cwebp(['-q 85']));
            }

            $this->optimizerChain->setTimeout(10);
            if (isset($this->logger)) {
                $this->optimizerChain->useLogger($this->logger);
            }
        }
        return $this->optimizerChain;
    }
}
