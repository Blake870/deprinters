<?php

namespace Swissup\Pagespeed\Model\Bundle;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Asset\File\FallbackContext as FileFallbackContext;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;
use Swissup\Pagespeed\Helper\Config as ConfigHelper;
use Magento\Deploy\Package\BundleInterface;
use Swissup\Pagespeed\Model\Bundle\Manager\RequireJs;
use Magento\Framework\View\Asset\Minification;

class Manager
{

    /**
     * Matched file extension name for JavaScript files
     */
    const ASSET_TYPE_JS = 'js';
    /**
     * Matched file extension name for template files
     */
    const ASSET_TYPE_HTML = 'html';

    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var FileFallbackContext
     */
    private $staticContext;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * Factory for Bundle object
     *
     * @see BundleInterface
     * @var Swissup\Pagespeed\Model\Bundle\Manager\RequireJsFactory
     */
    private $bundleFactory;

    /**
     * Helper class for static files minification related processes
     *
     * @var Minification
     */
    private $minification;

    /**
     * List of supported types of static files
     *
     * @var array
     * */
    public static $availableTypes = [
        self::ASSET_TYPE_JS,
        self::ASSET_TYPE_HTML
    ];

    /**
     * @param AssetRepository $assetRepo
     * @param \Magento\Framework\Filesystem $appFilesystem
     * @param ConfigHelper $configHelper
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Swissup\Pagespeed\Model\Bundle\Manager\RequireJsFactory $bundleFactory
     * @param Minification $minification
     */
    public function __construct(
        AssetRepository $assetRepo,
        \Magento\Framework\Filesystem $appFilesystem,
        ConfigHelper $configHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Swissup\Pagespeed\Model\Bundle\Manager\RequireJsFactory $bundleFactory,
        Minification $minification
    ) {
        $this->assetRepo = $assetRepo;
        $this->staticContext = $assetRepo->getStaticViewFileContext();
        $this->filesystem = $appFilesystem;
        $this->configHelper = $configHelper;
        $this->serializer = $serializer;
        $this->bundleFactory = $bundleFactory;
        $this->minification = $minification;

        $this->ensureSourceFiles();
    }

    /**
     *
     * @return string
     */
    private function getSubDir()
    {
        return 'Swissup_Pagespeed/js/bundle';
    }

    /**
     * Create a view assets representing the bundle js functionality
     *
     * @param  string $handle [description]
     * @return \Magento\Framework\View\Asset\File[]
     */
    public function createBundleJsPool($handle)
    {
        $bundles = [];
        $libDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        /** @var $context \Magento\Framework\View\Asset\File\FallbackContext */
        $context = $this->assetRepo->getStaticViewFileContext();

        $bundleDir = $context->getPath() . '/' . $this->getSubDir();

        if (!$libDir->isExist($bundleDir)) {
            return [];
        }

        $isMinifiedEnabled = $this->minification->isEnabled('js');
        foreach ($libDir->read($bundleDir) as $bundleFile) {
            if (pathinfo($bundleFile, PATHINFO_EXTENSION) !== 'js'
                || strpos($bundleFile, $bundleDir . '/' . $handle . '-bundle') !== 0
            ) {
                continue;
            }

            $isMinifiedFilename = $this->minification->isMinifiedFilename($bundleFile);
            if (($isMinifiedEnabled && !$isMinifiedFilename)
                || (!$isMinifiedEnabled && $isMinifiedFilename)
            ) {
                continue;
            }
            $relPath = $libDir->getRelativePath($bundleFile);
            $bundles[] = $this->assetRepo->createArbitrary($relPath, '');
        }
        return $bundles;
    }

    /**
     * Create a view asset representing the static js functionality
     *
     * @return \Magento\Framework\View\Asset\File|false
     */
    public function createStaticJsAsset()
    {
        return $this->assetRepo->createAsset(
            \Magento\Framework\RequireJs\Config::STATIC_FILE_NAME
        );
    }


    /**
     *
     * @return void
     */
    private function ensureSourceFiles()
    {
        $area = $this->staticContext->getAreaCode();
        $theme = $this->staticContext->getThemePath();
        $locale = $this->staticContext->getLocale();

        $bundleManager = $this->bundleFactory->create([
            'area' => $area,
            'theme' => $theme,
            'locale' => $locale
        ]);
        // $bundleManager->clear();

        $rjsConfig = $this->configHelper->getRjsJsonConfig();

        json_decode($rjsConfig, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $rjsConfig = [];
        } else {
        // try {
            $rjsConfig = $this->serializer->unserialize($rjsConfig, true);
        // } catch (\Zend_Json_Exception $e) {
        //     $rjsConfig = [];
        // }
        }

        $paths = isset($rjsConfig['paths']) ? $rjsConfig['paths'] : [];

        $handles = isset($rjsConfig['modules']) ? $rjsConfig['modules'] : [];

        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        foreach ($handles as $handle) {
            if (!isset($handle['name'])) {
                continue;
            }
            $bundleManager->setHandle($handle['name']);

            if ($bundleManager->isHandleAlreadyExist()) {
                continue;
            }

            $includeFiles = isset($handle['include']) ? $handle['include'] : [];
            if (empty($includeFiles)) {
                continue;
            }
            foreach ($includeFiles as $includeFile) {
                $includeFile = isset($paths[$includeFile]) ? $paths[$includeFile] : $includeFile;

                if (substr($includeFile, -1) === '!') {
                    continue;
                }
                if (strpos($includeFile, 'text!') === 0) {
                    $includeFile = substr($includeFile, 5);
                } else {
                    $includeFile = $includeFile . '.js';
                }

                $contentType = pathinfo($includeFile, PATHINFO_EXTENSION);
                if (!in_array($contentType, self::$availableTypes)) {
                    continue;
                }

                $includeFilePath = $this->staticContext->getPath() . '/' . $includeFile;
                if (!$dir->isExist($includeFilePath)) {
                    foreach ($paths as $short => $path) {
                        if (strpos($includeFile, $short) === 0) {
                            $includeFile = $path . substr($includeFile, strlen($short));
                        }
                    }
                }

                $includeFilePath = $this->staticContext->getPath() . '/' . $includeFile;
                if (!$dir->isExist($includeFilePath)) {
                    $includeMinFile = preg_replace('/\.js$/', '.min.js', $includeFile);
                    $includeFilePath = $this->staticContext->getPath() . '/' . $includeMinFile;
                    if (!$dir->isExist($includeFilePath)) {
                        continue;
                    }
                }

                $bundleManager->addFile($includeFile, $includeFilePath, $contentType);
            }
            $bundleManager->flush();
        }
    }
}
