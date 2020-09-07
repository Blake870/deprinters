<?php
namespace Swissup\Reviewreminder\Helper;

use Swissup\Reviewreminder\Model\Entity as ReminderModel;

class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CollectionFactory
     */
    protected $historyCollectionFactory;
    /**
     *
     * @var \Swissup\Reviewreminder\Model\EntityFactory
     */
    protected $reminderFactory;
    /**
     * @var Swissup\Reviewreminder\Helper\Config
     */
    protected $configHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailFactory;
    /**
     * @var Boolean manual email sending flag
     */
    private $isManualSend;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    protected $groupedType;
    /**
     * @param Context $context
     * @param Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Swissup\Reviewreminder\Helper\Config $configHelper
     * @param \Swissup\Reviewreminder\Model\EntityFactory $reminderFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Email\Model\TemplateFactory $emailFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedType
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Swissup\Reviewreminder\Helper\Config $configHelper,
        \Swissup\Reviewreminder\Model\EntityFactory $reminderFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Email\Model\TemplateFactory $emailFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedType
    )
    {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->configHelper = $configHelper;
        $this->reminderFactory = $reminderFactory;
        $this->date = $date;
        $this->orderFactory = $orderFactory;
        $this->productFactory = $productFactory;
        $this->appEmulation = $appEmulation;
        $this->imageHelper = $imageHelper;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->emailFactory = $emailFactory;
        $this->storeManager = $storeManager;
        $this->groupedType = $groupedType;
        parent::__construct($context);
    }
    /**
     * Get order created date or status change date depending from configuration
     * @param \Magento\Sales\Model\Order $order order instance
     * @return date order date
     */
    public function getOrderDate($order, $configHelper)
    {
        $orderHistoryCollection = $this->historyCollectionFactory->create()
            ->addAttributeToSelect('created_at')
            ->addAttributeToSort('created_at', 'ASC')
            ->addAttributeToFilter('parent_id', ['eq' => $order->getId()]);
        if ($configHelper->allowSpecificStatuses()) {
            $orderHistoryCollection->addAttributeToFilter('status',
                ['in' => $configHelper->specificOrderStatuses()])
            ->load();
            $orderDate = $orderHistoryCollection->getLastItem()->getCreatedAt();
            if (is_null($orderDate)) {
                $orderDate = $order->getUpdatedAt();
            }
        } else {
            $orderDate = $order->getCreatedAt();
        }
        return $orderDate;
    }
    /**
     * Send review reminders
     * @param Array $reminderIds array of ids when sent manually or null for cron
     */
    public function sendReminders($reminderIds)
    {
        $this->isManualSend = ($reminderIds != null);
        $reminderModel = $this->reminderFactory->create();
        $entityCollection = $reminderModel->getCollection()->clear();
        if ($this->isManualSend) {
            $entityCollection->addFieldToFilter('entity_id', ['in' => $reminderIds]);
        } else {
            $entityCollection->addFieldToFilter('status', ['eq' => ReminderModel::STATUS_NEW]);
        }
        // check if enough days passed to send reminder
        if (!$this->isManualSend) {
            $daysAfter = $this->configHelper->getSendEmailAfter();
            if (is_int($daysAfter) && $daysAfter > 0) {
                $checkDate = date("Y-m-d H:i:s", $this->date->timestamp(time() - $daysAfter * 24 * 60 * 60));
                $entityCollection->addFieldToFilter('order_date', ['lteq' => $checkDate]);
            }
        }
        $entityCollection->getSelect()
            ->reset('columns')
            ->columns([
                'entity_ids' => 'GROUP_CONCAT(entity_id SEPARATOR ",")',
                'order_ids' => 'GROUP_CONCAT(order_id SEPARATOR ",")',
                'customer_email'
            ])
            ->group('customer_email');
        if (!$this->isManualSend) {
            $entityCollection->getSelect()->limit($this->configHelper->getNumOfEmailsPerCron());
        }
        foreach ($entityCollection as $entity) {
            try {
                $this->processOrders($entity->getCustomerEmail(), $entity->getOrderIds(), $entity->getEntityIds());
                $this->changeOrdersStatus($reminderModel, $entity->getEntityIds(), ReminderModel::STATUS_SENT);
            } catch (\Exception $e) {
                $this->changeOrdersStatus($reminderModel, $entity->getEntityIds(), ReminderModel::STATUS_FAILED);
                throw new \Exception($e->getMessage());
            }
        }
    }

    private function changeOrdersStatus($model, $entityIds, $status)
    {
        if (strpos($entityIds, ',') === false) {
            $this->saveEntityStatus($model, $entityIds, $status);
        } else {
            $entityIdsArr = explode(',', $entityIds);
            foreach ($entityIdsArr as $entityId) {
                $this->saveEntityStatus($model, $entityId, $status);
            }
        }
    }
    /**
     * Save record status
     */
    private function saveEntityStatus($model, $entityId, $status)
    {
        $model->load($entityId)->setStatus($status);
        try {
            $model->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Go through orders and send emails
     * @param  String $customerEmail
     * @param  String $orderIds
     */
    private function processOrders($customerEmail, $orderIds, $entityIds)
    {
        $orderDataArr = [];
        if (strpos($orderIds, ',') === false) {
            $this->collectOrderData($orderIds, $orderDataArr);
        } else {
            $orderIdsArr = explode(',', $orderIds);
            foreach ($orderIdsArr as $orderId) {
                $this->collectOrderData($orderId, $orderDataArr);
            }
        }
        // remove duplicated products
        $orderDataArr = array_map("unserialize", array_unique(array_map("serialize", $orderDataArr)));
        $this->processEmail($customerEmail, $orderDataArr);
    }
    /**
     * Collect order data by order id
     * @param  int $orderId
     * @param  array &$orderDataArr Reference to data array
     */
    public function collectOrderData($orderId, &$orderDataArr)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $orderDataArr['customer_name'] = $order->getCustomerName();
        $orderDataArr['store_id'] = $order->getStoreId();
        $orderedItems = $order->getAllVisibleItems();
        $orderedProductIds = [];
        foreach ($orderedItems as $item) {
            array_push($orderedProductIds, $item->getData('product_id'));
        }
        $productCollection = $this->productFactory->create()
            ->getCollection()
            ->setStore($orderDataArr['store_id'])
            ->addIdFilter($orderedProductIds)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addUrlRewrite()
            ->load();
        //emulate frontend to get correct product image
        $this->appEmulation->startEnvironmentEmulation($orderDataArr['store_id']);
        foreach($productCollection as $product) {
            if (!$product->isVisibleInSiteVisibility() &&
                $parentsIds = $this->groupedType->getParentIdsByChild($product->getId()))
            {
                $product = $this->productFactory->create()->load($parentsIds[0]);
            }

            $productUrl = $product->getUrlInStore(
                ['_scope' => $orderDataArr['store_id'], '_nosid' => true]
            );
            $productImageUrl = $this->imageHelper
                ->init($product, 'image')
                ->setImageFile($product->getImage())
                ->resize(100)
                ->getUrl();
            array_push($orderDataArr, [
                'id' => $product->getId(),
                'url' => $productUrl,
                'name' => $product->getName(),
                'image' => $productImageUrl
            ]);
        }
        $this->appEmulation->stopEnvironmentEmulation();
    }
    /**
     * Get reminder email subject and fill variables
     * @return String email subject
     */
    public function filterEmailSubject($customerName, $productName, $storeId)
    {
        $subject = $this->configHelper->getEmailSubject($storeId);
        $subject = str_replace("{customer_name}", $customerName, $subject);
        $subject = str_replace("{product_name}", $productName, $subject);
        return $subject;
    }
    /**
     * Get list of products links for email
     * @return String
     */
    public function getProductsList($data)
    {
        $products = '';
        foreach ($data as $product) {
            if ($product['url'] && $product['name']) {
                $products .= "<a href='" . $product['url'] . "'>" . $product['name'] . "</a>, ";
            }
        }
        return $products;
    }
    /**
     * Prepare email variables and options
     * @param  String $customerEmail
     * @param  Array $emailData Reminder email data
     * @param  Bool $preview if true return email template else send email
     */
    private function processEmail($customerEmail, $emailData, $preview = false)
    {
        $customerName = $emailData['customer_name'];
        $storeId = $emailData['store_id'];
        $productName = $emailData[0]['name'];
        unset($emailData['customer_name']);
        unset($emailData['store_id']);
        $productsList = $this->getProductsList($emailData);
        $subject = $this->filterEmailSubject($customerName, $productName, $storeId);
        $vars = [
            'subject' => $subject,
            'products' => $emailData,
            'customer_name' => $customerName,
            'products_list' => $productsList
        ];
        $templateId = $this->configHelper->getEmailTemplate($storeId);
        $from = $this->configHelper->getEmailSendFrom($storeId);
        $to = [
            'email' => $customerEmail,
            'name' => $customerName
        ];
        if ($preview) {
            return $this->_previewEmail($templateId, $vars, $storeId);
        } else {
            return $this->_sendEmail($from, $to, $templateId, $vars, $storeId);
        }
    }
    /**
     * Send email to customer
     * @param  String $from             send email from
     * @param  String $to               send email to
     * @param  String|int $templateId   email template identifier
     * @param  Array $vars              email template variables
     * @param  int $storeId             order store id
     * @param  String $area             email area
     * @return Bool                     true
     */
    private function _sendEmail($from, $to, $templateId, $vars, $storeId, $area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        if (!$this->isManualSend && !$this->configHelper->isEnabled($storeId)) {
            return $this;
        }

        $this->inlineTranslation->suspend();
        $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => $area,
                'store' => $storeId
            ])
            ->setTemplateVars($vars)
            ->setFrom($from)
            ->addTo($to['email'], $to['name']);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
        return true;
    }
    /**
     * Generate email template preview
     * @param  String|int $templateId   email template identifier
     * @param  Array $vars              email template variables
     * @param  int $storeId             order store id
     * @param  String $area             email area
     * @return String                   generated email template html
     */
    private function _previewEmail($templateId, $vars, $storeId, $area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        /** @var $template \Magento\Email\Model\Template */
        $template = $this->emailFactory->create();
        $template->setTemplateText($template->getTemplateText());
        $template->setVars($vars);
        $template->setId($templateId);
        $template->setOptions([
            'area' => $area,
            'store' => $storeId
        ]);
        $template->load($templateId);
        $template->emulateDesign($storeId);
        $this->appEmulation->startEnvironmentEmulation($storeId);
        $templateProcessed = $template->processTemplate();
        $this->appEmulation->stopEnvironmentEmulation();
        $template->revertDesign();
        return $templateProcessed;
    }
    /**
     * Get email template preview
     * @param  int $orderId             order id
     * @param  String $customerEmail    customer email
     * @return String                   generated email template html
     */
    public function getEmailPreviewHtml($orderId, $customerEmail)
    {
        $orderDataArr = [];
        $this->collectOrderData($orderId, $orderDataArr);
        // remove duplicated products
        $orderDataArr = array_map("unserialize", array_unique(array_map("serialize", $orderDataArr)));
        return $this->processEmail($customerEmail, $orderDataArr, true);
    }
}
