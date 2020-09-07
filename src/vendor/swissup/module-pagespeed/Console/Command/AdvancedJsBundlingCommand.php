<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
// use Swissup\Pagespeed\Service\ImageResize;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Framework\ObjectManagerInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Exception\LocalizedException;

class AdvancedJsBundlingCommand extends \Symfony\Component\Console\Command\Command
{
    const STORE = 'store';

    /**
     * @var State
     */
    // private $appState;

    /**
     * @var ObjectManagerInterface
     */
    // private $objectManager;

    private $storeManager;

    private $localeResolver;

    private $themeProvider;

    private $filesystem;

    private $varDirectory;

    private $staticViewDirectory;

    /**
     * @param State $appState
     * @param ImageResize $resize
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        // \Magento\Store\Api\Data\StoreInterface $store
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        Filesystem $filesystem
    //     State $appState,
    //     ImageResize $resize,
    //     ObjectManagerInterface $objectManager
    ) {
        parent::__construct();
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->themeProvider = $themeProvider;
        $this->filesystem = $filesystem;
    //     $this->appState = $appState;
    //     $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption(
            self::STORE,
            '-s',
            InputOption::VALUE_REQUIRED, // InputOption::VALUE_OPTIONAL,
            'Generate for some store.'
            // 0
        );

        $this->setName('swissup:pagespeed:js:bundling')
            ->setDescription('Advanced bundling https://devdocs.magento.com/guides/v2.3/performance-best-practices/advanced-js-bundling.html')
            ;

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $storeId = $input->getArgument(self::STORE) ?: 0;
        $storeId = $input->getOption(self::STORE) ?: 0;

        $store = $this->storeManager->getStore($storeId);
        // $output->writeln($store->getBaseUrl());
        $localeCode = $this->localeResolver->getLocale();

        $themeId = $store->getConfig(\Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID);
        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->themeProvider->getThemeById($themeId);

        $themeCode = $theme->getCode();
        $output->writeln('<info>Theme : ' . $themeCode . '</info>');
        $output->writeln('<info>Locale : ' . $localeCode . '</info>');

        $staticViewDirectory = $this->getStaticViewDirectory();

        // print_r(get_class($staticViewDirectory));
        // print_r(get_class_methods($staticViewDirectory));
        // die;

        $staticContentPath = 'frontend' . DIRECTORY_SEPARATOR . $themeCode . DIRECTORY_SEPARATOR . $localeCode;
        if (!$staticViewDirectory->isDirectory($staticContentPath)) {
            throw new LocalizedException(
                __(
                    'Please generate static store content'
                    . PHP_EOL . 'If you still want to deploy in these modes, use -f option: ' . PHP_EOL
                    . "'bin/magento setup:static-content:deploy -f -a frontend --theme " . $themeCode . " -l " . $localeCode . "'"
                )
            );
        }
        // $output->writeln($staticViewDirectory->getAbsolutePath($staticContentPath));
        // return;

        $baseUrl = $store->getBaseUrl();
        // $baseUrl = 'https://swissupdemo.com/pagespeed/current/';
        $bundles = [
            'default' => $baseUrl,
            'cart' => $baseUrl . 'checkout/cart/',
            'checkout' => $baseUrl . 'checkout/',
            'catalog' => $baseUrl . '/women/tops-women/jackets-women.html',
            'product' => $baseUrl . '/olivia-1-4-zip-light-jacket.html',
        ];


        // generate phantomjs script file
        $phantomjsOptions = '--disk-cache=true --ignore-ssl-errors=true --web-security=false --webdriver-loglevel=ERROR';
        $phantomjsScriptFile = 'phantomjs-script.js';
        $varDirectory = $this->getVarDirectory();
        if (!$varDirectory->isFile($phantomjsScriptFile)) {
            $varDirectory->writeFile(
                $phantomjsScriptFile,
                $this->getPhantomEvulateScriptCode()
            );
        }
        $output->writeln($varDirectory->getAbsolutePath($phantomjsScriptFile));
        $phantomjsScriptFile = $varDirectory->getAbsolutePath($phantomjsScriptFile);

        $phantomjsCommand = "phantomjs {$phantomjsOptions} {$phantomjsScriptFile}";
        // $phantomjsEvalcode = '"JSON.stringify(Object.keys(window.require.s.contexts._.defined))"';
        $phantomjsEvalcode = '"Object.keys(window.require.s.contexts._.defined)"';

        $_bundles = [];
        foreach ($bundles as $bundle => $url) {
            $command = $phantomjsCommand . ' ' . $url . ' ' . $phantomjsEvalcode;
            $result = $this->runCommand($command);

            $result = trim($result, " \n\t");
            $result = str_replace("\t\n", '', $result);
            $result = explode(',', $result);
            $result = array_filter($result);
            $result = array_unique($result);
            $result = array_filter($result, function ($var) {
                return (stripos($var, 'mixins') === false);
            });
            $result = array_values($result);
            // print_r($result);
            // print_r(json_encode($result));
            // print_r(json_encode($result));
            // die;
            $result = json_encode($result);
            $result = str_replace("\/", '/', $result);

            $_bundles[$bundle] = $result;
            // $output->writeln($result);
        }

        $partKeys = ['deps', 'shim', 'paths', 'map'];

        foreach ($partKeys as $partKey) {
            $phantomjsEvalcode = '"JSON.stringify(window.require.s.contexts._.config.' . $partKey . ')"';
            $command = $phantomjsCommand . ' ' . $url . ' ' . $phantomjsEvalcode;
            $_bundles[$partKey] = $this->runCommand($command);
            // $output->writeln($bundles[$partKey]);
        }

        if ($_bundles['deps'] === '[]') {
            $_bundles['deps'] = '[' . implode(', ', [
                'jquery/jquery.cookie',
                'jquery/jquery-migrate',
                'jquery/jquery.mobile.custom',
                'mage/common',
                'mage/dataPost',
                'mage/bootstrap',
                'mage/translate-inline',
                'Magento_Theme/js/responsive',
                'Magento_Theme/js/theme',
            ]) . ']';
        }


        foreach ($_bundles as &$value) {
            $value = str_replace(",", ",\n", $value);
        }

        $requirejsBuildFile = 'requirejs-build.js';
        // $varDirectory = $this->getVarDirectory();
        // if (!$varDirectory->isFile($requirejsBuildFile)) {
            $contentRequireJsBuildFile = $this->getRequireJsBuildCode($_bundles);
            // Fixes
            $contentRequireJsBuildFile = str_replace('mage/requirejs/text', 'requirejs/text', $contentRequireJsBuildFile);
            $contentRequireJsBuildFile = str_replace(',
"paypalInContextExpressCheckout":"https://www.paypalobjects.com/api/checkout"', '', $contentRequireJsBuildFile);
            $contentRequireJsBuildFile = str_replace('"braintree":"https://js.braintreegateway.com/js/braintree-2.32.0.min.js",', '', $contentRequireJsBuildFile);

            $varDirectory->writeFile(
                $requirejsBuildFile,
                $contentRequireJsBuildFile
                // $this->getRequireJsBuildCode2()
            );
        // }
        $requirejsBuildFile = $varDirectory->getAbsolutePath($requirejsBuildFile);
        $output->writeln('<info>Generated ' . $requirejsBuildFile . '</info>');

        //Move static content to tmp dir
        $path = $staticViewDirectory->getAbsolutePath($staticContentPath);
        $staticViewDirectory->getDriver()->rename($path, $path . '_tmp');
        /*
            tar cvzf Magento-luma-en_US.tgz pub/static/frontend/Magento/luma/en_US
            tar -xvf Magento-luma-en_US.tgz -C .

         */

        $output->writeln("<info>Moved static content from {$path} to {$path}_tmp</info>");


        $command = "r.js -o {$requirejsBuildFile} baseUrl={$path}_tmp dir={$path}";
        $output->writeln("Running : {$command}");
        $commandOutput = $this->runCommand($command);
        $output->writeln($commandOutput);

        $output->write(PHP_EOL);
        $output->writeln("<info>Successfully</info>");
    }

    /**
     *
     * @param  string $command
     * @return string
     */
    private function runCommand($command)
    {
        $process = new Process($command);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getVarDirectory()
    {
        if ($this->varDirectory === null) {
            $this->varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            // $this->varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        }

        return $this->varDirectory;
    }


    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getStaticViewDirectory()
    {
        if ($this->staticViewDirectory === null) {
            $this->staticViewDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        }

        return $this->staticViewDirectory;
    }

    /**
     *
     * @return string
     */
    private function getPhantomEvulateScriptCode()
    {
        return '"use strict";
var page = require(\'webpage\').create(),
    system = require(\'system\'),
    address,
    jsCode;


if (system.args.length === 1) {
    console.log(\'Usage: $phantomjs deps.js url\');
    phantom.exit(1);
} else {
    address = system.args[1];
    page.open(address, function (status) {
        if (status !== \'success\') {
            console.log(\'FAIL to load the address : \' + address);
        }
        jsCode = system.args[2] ? system.args[2] : \'Object.keys(window.require.s.contexts._.defined)\';
        setTimeout(function () {
            console.log(
                page.evaluate(
                    function (js) {
                        return eval(js);
                    }, jsCode
                )
            );
            phantom.exit(0);
        }, 3000);
    });
}';
    }

    private function getRequireJsBuildCode($bundles)
    {
        return "{
    // Enable js minification with Uglify. Uncomment this out during development tomake builds faster
    //optimize: \'none\',
    //generateSourceMaps: true,
    generateSourceMaps: false,
    // 'generateSourceMaps': true,
    preserveLicenceComments: false,
    optimize: 'uglify2',
    inlineText: true,
    'wrapShim': true,
    // Files that are required for all pages will be included in require.js file
    // requirejs.s.contexts._.config.deps
    deps: {$bundles['deps']},
    // Shim configuration for non-AMD modules. Copied from requirejs-config
    // requirejs.s.contexts._.config.shim
    shim: {$bundles['shim']},
    // Copied from requirejs-config to load modules for bundling correctly
    // requirejs.s.contexts._.config.paths
    paths: {$bundles['paths']},
    // Copied from requirejs-config to load modules for bundling correctly
    // requirejs.s.contexts._.config.map
    map: {$bundles['map']},
    // Bundles that will be generated by r.js
    modules: [
        // Bundle all dependencies common for all pages to requirejs/require.js file. The bundled file will be loaded on all pages.
        {
            name: 'requirejs/require',
        },
        // Bundle all dependencies common for all pages that have 'default' handle (home, product, category, etc)
        // Exclude requirejs/require, as it will be loaded in separate request.
        // Edit this bundle configuration if you add/remove static files in default handle.
        {
            name: 'bundles/default',
            create: true,
            include: {$bundles['default']},
            exclude: [
                'requirejs/require'
            ]
        },
        // Cart page bundle
        {
            name: 'bundles/cart',
            create: true,
            include: {$bundles['cart']},
            exclude: [
                'bundles/default',
                'requirejs/require'
            ]
        },
        // Checkout bundle. Default bundle is not loaded on checkout
        // Edit this bundle if you customize checkout
        {
            name: 'bundles/checkout',
            create: true,
            include: {$bundles['checkout']},
            exclude: [
                'requirejs/require'
            ]
        },
        // Bundle for all catalog pages (Category, Product)
        {
            name: 'bundles/catalog',
            create: true,
            include: {$bundles['catalog']},
            exclude: [
                'bundles/default',
                'requirejs/require'
            ]
        },
        // Product page bundle
        {
            name: 'bundles/product',
            create: true,
            include: {$bundles['product']},
            exclude: [
                'bundles/default',
                'bundles/catalog',
                'requirejs/require'
            ]
        }
    ],
    // onModuleBundleComplete: function (data) {
    //    function onBundleComplete (config, data) {
    //         const fileName = `\${config.dir}requirejs-config.js`;
    //         const minFileName = `\${config.dir}requirejs-config.min.js`;
    //         const bundleConfig = {};
    //         var fileContents = '';
    //         bundleConfig[data.name] = data.included;
    //         bundleConfig[data.name] = bundleConfig[data.name].map(bundle => bundle.replace(/\.js$/, ''));
    //         var contents = `require.config({
    //             bundles: \${JSON.stringify(bundleConfig)},
    //         });`;

    //         // file.appendFile(fileName, contents);
    //         fileContents = file.readFile(fileName) + '\n' + contents;
    //         file.saveUtf8File(fileName, fileContents);

    //         if (file.exists(minFileName)) {
    //             fileContents = file.readFile(minFileName) + '\n' + contents;
    //             file.saveUtf8File(minFileName, fileContents);
    //         }
    //    }
    //    onBundleComplete(config, data);
    // }
    //     'Swissup_Pagespeed/js/bundles/default': [
    //         ...
    //     ]
    //     'Swissup_Pagespeed/js/bundles/catalog-category-view': [
    //         'addToWishlist',
    //         ...
    //         'slide'
    //     ]
    // }
}";
    }
}
