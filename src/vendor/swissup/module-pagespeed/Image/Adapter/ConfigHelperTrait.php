<?php
namespace Swissup\Pagespeed\Image\Adapter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Swissup\Pagespeed\Helper\Config;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

trait ConfigHelperTrait
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * override \Magento\Framework\Image\Adapter\AbstractAdapter
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger,
        \Swissup\Pagespeed\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->logger = $logger;
        $this->directoryWrite = $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->configHelper = $configHelper;
    }
}
