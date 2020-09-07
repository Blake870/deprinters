<?php

namespace Swissup\Pagespeed\Block\Html\Head;

use Swissup\Pagespeed\Model\Bundle\Manager as BundleFileManager;
use Swissup\Pagespeed\Helper\Config as ConfigHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\RequireJs\Config as RequireJsConfig;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Context as ViewElementContext;
use Magento\Framework\View\Page\Config as PageConfig;

class Config extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var BundleFileManager
     */
    private $fileManager;

    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var DirectoryList
     */
    private $dir;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var ConfigHelper
     */
    private $configHelper;


    /**
     * @param ViewElementContext $context
     * @param AppState $appState
     * @param BundleFileManager $fileManager
     * @param PageConfig $pageConfig
     * @param ConfigHelper $configHelper
     * @param array $data
     */
    public function __construct(
        ViewElementContext $context,
        AppState $appState,
        BundleFileManager $fileManager,
        PageConfig $pageConfig,
        DirectoryList $dir,
        HttpRequest $httpRequest,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->fileManager = $fileManager;
        $this->pageConfig = $pageConfig;
        $this->appState = $appState;
        $this->dir = $dir;
        $this->httpRequest = $httpRequest;
        $this->configHelper = $configHelper;
    }

    /**
     * Include specified AMD bundle as an asset on the page
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if (!$this->configHelper->isAdvancedJsBundling()) {
            return parent::_prepareLayout();
        }
        // return parent::_prepareLayout();
        $after = RequireJsConfig::REQUIRE_JS_FILE_NAME;
        $assetCollection = $this->pageConfig->getAssetCollection();
        $fullActionName = $this->httpRequest->getFullActionName();
        $fullActionName = str_replace("_", "-", $fullActionName);
        $staticAsset = false;
        $handles = ['default', $fullActionName];
        foreach ($handles as $handle) {
            $bundleAssets = $this->fileManager->createBundleJsPool($handle);

            /** @var \Magento\Framework\View\Asset\File $bundleAsset */
            if (!empty($bundleAssets)) {
                $bundleAssets = array_reverse($bundleAssets);
                foreach ($bundleAssets as $bundleAsset) {
                    $assetCollection->insert(
                        $bundleAsset->getFilePath(),
                        $bundleAsset,
                        $after
                    );
                }
                $after = reset($bundleAssets)->getFilePath();
            }
        }
        $staticAsset = $this->fileManager->createStaticJsAsset();
        if ($staticAsset !== false) {
            $assetCollection->insert(
                $staticAsset->getFilePath(),
                $staticAsset,
                $after
            );
        }

        return parent::_prepareLayout();
    }
}
