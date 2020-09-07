<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Framework;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Swissup\Ajaxpro\CustomerData\AbstractCustomerData;

class Product extends AbstractCustomerData
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Page\Config\RendererFactory
     */
    // protected $pageConfigRendererFactory;
    /**
     * Manager messages
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param \Swissup\Ajaxpro\Helper\Config $configHelper
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param MessageManager $messageManager
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader,
        \Swissup\Ajaxpro\Helper\Config $configHelper,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $coreRegistry,
        MessageManager $messageManager,
        array $data = []
    ) {
        parent::__construct(
            $layoutFactory,
            $layoutBuilderFactory,
            $context,
            $pageLayoutReader,
            $configHelper,
            $data
        );
        $this->productRepository = $productRepository;
        $this->storeManager = $context->getStoreManager();
        $this->coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->configHelper->isProductViewEnable() ||
            !in_array('ajaxpro-product', $this->getSectionNames())
        ) {
            return [];
        }

        $ajaxpro = $this->getRequest()->getParam('ajaxpro');

        $return  = [];
        if (isset($ajaxpro['product_id'])) {
            try {
                $this->getRequest()->setParam('id', $ajaxpro['product_id']);
                $product = $this->productRepository->getById(
                    $ajaxpro['product_id'],
                    false,
                    $this->storeManager->getStore()->getId()
                );

                $this->coreRegistry->register('current_product', $product);
                $this->coreRegistry->register('product', $product);
            } catch (NoSuchEntityException $e) {
                return [];
            }
            $productHandles = [];

            if ($product) {
                $urlSafeSku = rawurlencode($product->getSku());
                $productHandles = array_merge(
                    ['default', 'ajaxpro_catalog_product_view'],
                    $this->generatePageLayoutHandles(
                        ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()],
                        'catalog_product_view'
                    )
                );
            }

            $return  = [
                // 'params' => $ajaxpro,
                // 'test' => md5(time()),
                'catalog.product.view' => $this->getBlockHtml(
                    'content',
                    // 'product.info.main',
                    $productHandles
                ),
                'catalog.product.view.handles' => $productHandles,
                'reinit' => $this->getBlockHtml('ajaxpro.init', ['default'])
            ];
            $this->flushLayouts();
        }
        return $return;
    }
}
