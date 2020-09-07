<?php
namespace Swissup\Ajaxpro\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Http\Header\HeaderInterface as HttpHeaderInterface;

class AddProductUrlToAjaxResponseObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Swissup\Ajaxpro\Helper\Config
     */
    protected $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param \Swissup\Ajaxpro\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        \Swissup\Ajaxpro\Helper\Config $configHelper
    ) {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->storeManager =  $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isProductViewEnable()) {
            return $this;
        }

        $event = $observer->getEvent();
        /** @var $request \Magento\Framework\App\RequestInterface */
        $request = $event->getRequest();
        if (!$request->isAjax() || $request->getParam('return_url')) {
            return $this;
        }

        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $event->getControllerAction();
        /** @var $response \Magento\Framework\App\ResponseInterface */
        $response = $controller->getResponse();

        $resultJsonData = $response->getContent();
        if (empty($resultJsonData)) {
            return $this;
        }
        json_decode($resultJsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this;
        }
        $resultJsonData = $this->serializer->unserialize($resultJsonData);

        if (!isset($resultJsonData['backUrl'])) {
            return $this;
        }

        $product = $this->initProduct($request);
        if (!$product || !$this->isProductHasOptions($product)) {
            return $this;
        }
        $resultJsonData['action'] = $request->getFullActionName();

        $additional = [];
        $additional['_escape'] = true;
        $additional['_query'] = [];
        $additional['_query']['options'] = 'cart';
        $resultJsonData['ajaxpro']['product'] = [
            'id' => $product->getId(),
            'product_url' => $product->getUrlModel()->getUrl($product, $additional),
            'has_options' => true
        ];

        // $cacheControlHeader = $response->getHeader('Cache-Control');

        $resultJsonData = $this->serializer->serialize($resultJsonData);
        $response
            // ->clearHeaders()
            ->clearHeader('Location')
            ->setHttpResponseCode(200)
            ->representJson($resultJsonData)
            // ->send()
            ;
        // if ($cacheControlHeader instanceof HttpHeaderInterface) {
        //     $response->setHeader('Cache-Control', $cacheControlHeader->getFieldValue());
        // }
        // exit;
        return $this;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function initProduct(\Magento\Framework\App\RequestInterface $request)
    {
        $productId = (int) $request->getParam('product');
        if ($productId) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     *
     * @param  \Magento\Catalog\Model\Product  $product
     * @return boolean
     */
    protected function isProductHasOptions(\Magento\Catalog\Model\Product $product)
    {
        if (null === $product) {
            $product = $this->initProduct();
        }
        if ($product) {
            if ($product->getTypeID() === 'grouped') {
                return true;
            }
            $typeInstance = $product->getTypeInstance();
            return $typeInstance && ($typeInstance->hasRequiredOptions($product)
                || $typeInstance->hasOptions($product));
        }

        return false;
    }
}
