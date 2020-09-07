<?php
namespace Swissup\Amp\Block;

use Magento\Framework\Component\ComponentRegistrar;

class Scss extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'SWISSUP_AMP_BLOCK_SCSS';

    protected $items = [];

    /**
     * @var \Leafo\ScssPhp\Compiler
     */
    protected $scss;

    /**
     * @var \Swissup\Rtl\Helper\Data
     */
    protected $rtlHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Leafo\ScssPhp\Compiler $scss
     * @param \Swissup\Rtl\Helper\Data $rtlHelper
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Leafo\ScssPhp\Compiler $scss,
        \Swissup\Rtl\Helper\Data $rtlHelper,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        ComponentRegistrar $componentRegistrar,
        array $data = []
    ) {
        $this->scss = $scss;
        $this->rtlHelper = $rtlHelper;
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
        parent::__construct($context, $data);
    }

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(['cache_lifetime' => false]);
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            self::CACHE_TAG,
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl(),
            'template' => $this->getTemplate(),
            implode(',', $this->getItems()),
        ];
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');

        $scssFolder = $this->getModuleScssDir('Swissup_Amp');
        $this->scss->setImportPaths([
            $this->getActiveThemeScssDir(),
            $scssFolder,
            $scssFolder . 'fallback'
        ]);

        // load main.scss first
        $mainAbsolutePath = $scssFolder . 'main.scss';
        $directoryRead = $this->readFactory->create($scssFolder);
        $mainFilePath = $directoryRead->getRelativePath($mainAbsolutePath);
        $string = $directoryRead->readFile($mainFilePath);

        if ($this->rtlHelper->isRtl()) {
            $string = "@import \"abstracts/rtl\";\n" . $string;
        }

        $items = $this->getItems();
        array_push($items, 'theme', 'custom');
        foreach ($items as $item) {
            list($module, $filePath) = $this->_assetRepo->extractModule($item);
            if (!empty($module)) {
                $this->scss->addImportPath($this->getModuleScssDir($module));
                $item = $filePath;
            }

            $string .= "@import \"{$item}\";\n";
        }

        try {
            $styles = $this->scss->compile($string);
        } catch (\Exception $e) {
            $styles = '';
            $this->_logger->error($e);
        }

        /** @var \Magento\Framework\DataObject */
        $transportObject = new \Magento\Framework\DataObject(
            [
                'styles' => $styles,
            ]
        );
        $this->_eventManager->dispatch(
            'swissup_amp_block_scss_after_compile',
            [
                'block' => $this,
                'transport' => $transportObject
            ]
        );
        $styles = $transportObject->getStyles();

        return $styles;
    }

    /**
     * Get module scss dir by module id
     * @param  string $moduleId
     * @return string
     */
    protected function getModuleScssDir($moduleId)
    {
        $moduleDir = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleId
        );

        return $moduleDir . '/view/frontend/web/css/scss/';
    }

    /**
     * Get path to amp scss dir in current theme
     * @return string
     */
    protected function getActiveThemeScssDir() {
        $theme = $this->getLayout()->getUpdate()->getTheme();
        $themeDir = $this->componentRegistrar->getPath(
            ComponentRegistrar::THEME,
            $theme->getFullPath()
        );

        return $themeDir . '/Swissup_Amp/web/css/scss';
    }

    public function addItem($name)
    {
        $this->items[$name] = $name;
    }

    public function getItems()
    {
        return $this->items;
    }
}
