<?php
namespace Swissup\Amp\Plugin\Checkout;

class ControllerCartAdd
{
    /**
     * @var \Swissup\Amp\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $checkoutHelper;

    /**
     * @var \Swissup\Amp\Helper\Message
     */
    protected $messageHelper;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Swissup\Amp\Helper\Data $helper
     * @param \Magento\Checkout\Helper\Cart $checkoutHelper
     * @param \Swissup\Amp\Helper\Message $messageHelper
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Swissup\Amp\Helper\Data $helper,
        \Magento\Checkout\Helper\Cart $checkoutHelper,
        \Swissup\Amp\Helper\Message $messageHelper,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->checkoutHelper = $checkoutHelper;
        $this->messageHelper = $messageHelper;
        $this->redirect = $redirect;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * Modify add to cart response
     * @param  \Magento\Checkout\Controller\Cart\Add $subject
     * @param  mixed $originalResult
     * @return bool
     */
    public function afterExecute(
        \Magento\Checkout\Controller\Cart\Add $subject,
        $originalResult
    ) {
        $request = $subject->getRequest();
        if (!$request->isPost() || !$request->getQuery('amp')) {
            return $originalResult;
        }

        $redirectTo = false;
        $response = $subject->getResponse();
        foreach ($response->getHeaders() as $header) {
            if (is_array($header) && $header['name'] === 'Location') {
                $redirectTo = $header['value'];
                break;
            }
        }
        if (!$redirectTo) {
            if ($this->helper->shouldRedirectToCart()) {
                $redirectTo = $this->checkoutHelper->getCartUrl();
            } else {
                $redirectTo = $this->redirect->getRedirectUrl();
            }
        }
        $redirectTo = str_replace('http://', 'https://', $redirectTo);

        $result = [
            'success'  => true,
            'messages' => []
        ];

        if ($this->messageHelper->hasFailureMessages()) {
            $result['success']  = false;
            $result['messages'] = $this->messageHelper->getMessages(true, true, true);
        } else {
            $response->setHeader('AMP-Redirect-To', $redirectTo);
            $product = $this->productRepository->getById(
                (int)$request->getParam('product'),
                false,
                $this->storeManager->getStore()->getId()
            );
            $result['messages']['success'] = [
                __('You added %1 to your shopping cart.', $product->getName())
            ];
        }

        $response
            ->clearHeader('Location')
            ->setHttpResponseCode($result['success'] ? 200 : 400)
            ->setHeader('Content-Type', 'application/json')
            // https://github.com/ampproject/amphtml/blob/master/spec/amp-cors-requests.md#ensuring-secure-responses
            ->setHeader('Access-Control-Allow-Origin', $this->helper->getAmpCacheDomainName())
            ->setHeader('Access-Control-Allow-Credentials', 'true')
            ->setHeader(
                'Access-Control-Expose-Headers',
                'AMP-Access-Control-Allow-Source-Origin,AMP-Redirect-To'
            )
            ->setHeader(
                'Amp-Access-Control-Allow-Source-Origin',
                $request->getScheme() . '://' . $request->getHttpHost()
            )
            ->setBody(json_encode($result));
    }
}
