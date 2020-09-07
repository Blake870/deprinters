<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Framework;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

abstract class AbstractCustomerData extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Magento\Framework\View\Layout\BuilderFactory
     */
    protected $layoutBuilderFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Layout\Reader
     */
    protected $pageLayoutReader;

    /**
     * @var \Swissup\Ajaxpro\Helper\Config
     */
    protected $configHelper;

    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $contextLayout;

    /**
     *
     * @var array
     */
    protected $layouts = [];

    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param \Swissup\Ajaxpro\Helper\Config $configHelper
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader,
        \Swissup\Ajaxpro\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        $this->pageConfig = $context->getPageConfig();
        $this->pageLayoutReader = $pageLayoutReader;
        $this->configHelper = $configHelper;
        $this->request = $context->getRequest();
        $this->contextLayout = $context->getLayout();
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $return  = [];

        // foreach ($return as $key => &$block) {
        //     $block .= '<script type="text/javascript">console.log("'
        //         . $key . ' ' . md5($block)
        //         . '");</script>';
        // }
        $this->flushLayouts();

        return $return;
    }

    /**
     * Retrieve subtotal block html
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getBlockHtml($blockName, $handles)
    {
        $layout = $this->getCustomLayoutByHandles($handles);

        $block = $layout->getBlock($blockName);

        if ($block) {
            $html = $block->toHtml();
        } else {
            $html = $layout->renderNonCachedElement($blockName);
        }

        $isDebugEnabled = false;
        $html = (!empty($html) ? $html : ($isDebugEnabled ? (' block not exist \'' . $blockName . '\' ' . time()
            . "\n xml <!-- " . $layout->getUpdate()->asString() . " -->"
            . "\n output <!--  " . $layout->getOutput() . " -->"
            . "\n handles <!--  " . implode(", ", $layout->getUpdate()->getHandles()) . " -->"
            ) : ''))
        ;
        // $layout->__destruct();

        return $html;
    }

    /**
     *
     * @param \Magento\Framework\View\Layout $layout
     * @return \Magento\Framework\View\Layout\View\Layout\Builder
     */
    private function getLayoutBuilder($layout)
    {
        $builder = $this->layoutBuilderFactory->create(
            \Swissup\Ajaxpro\Model\View\Layout\Builder::TYPE_PAGE,
            [
                'layout' => $layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader
            ]
        );

        return $builder;
    }

    /**
     *
     * @param  array $handles
     * @return \Magento\Framework\View\Layout
     */
    protected function getCustomLayoutByHandles($handles)
    {
        $key = implode(':', $handles);
        if (!isset($this->layouts[$key])) {
            $fullActionName = end($handles);

            $layout = $this->layoutFactory->create();

            $builder = $this->getLayoutBuilder($layout);

            $builder
                ->setCustomHandles($handles)
                ->setFullActionName($fullActionName);
            $layout->setBuilder($builder);

            $this->prepareContextLayout($handles);
            // $layout->publicBuild();

            $this->layouts[$key] = $layout;
        }

        return $this->layouts[$key];
    }

    /**
     * Add because context layout used in some 'custom' block
     * Magento\Catalog\Block\Product\View\Options\AbstractOptions
     * and Magento\Framework\Pricing\Render
     * Fix #22 circular dependency
     *
     * @param  array $handles
     * @return void
     */
    protected function prepareContextLayout($handles)
    {
        $update = $this->contextLayout->getUpdate();
        foreach ($update->getHandles() as $handle) {
            $update->removeHandle($handle);
        }
        foreach ($handles as $handle) {
            $update->addHandle($handle);
        }

        $this->contextLayout->isCacheable();
    }

    /**
     *
     */
    protected function flushLayouts()
    {
        foreach ($this->layouts as $layout) {
            $layout->__destruct();
        }
        return $this;
    }

    /**
     *
     * @param array|null $parameters page parameters
     * @param string|null $defaultHandle
     * @return bool
     */
    public function generatePageLayoutHandles(array $parameters = [], $defaultHandle = null)
    {
        $handle = $defaultHandle ? $defaultHandle : $this->getDefaultLayoutHandle();
        $pageHandles = [$handle];
        foreach ($parameters as $key => $value) {
            $pageHandles[] = $handle . '_' . $key . '_' . $value;
        }
        return $pageHandles;
    }

    /**
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @return array
     */
    protected function getSectionNames()
    {
        $sectionNames = $this->getRequest()->getParam('sections');
        $sectionNames = $sectionNames ? array_unique(\explode(',', $sectionNames)) : [];

        return $sectionNames;
    }
}
