<?php
namespace Swissup\Askit\Model;

use Swissup\Askit\Api\Data\MessageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Swissup\Askit\Model\Message\Status as MessageStatus;
use Swissup\Askit\Api\Data\ItemInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Message extends \Magento\Framework\Model\AbstractModel implements MessageInterface, IdentityInterface, CustomAttributesDataInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'askit_message';

    /**
     * @var string
     */
    protected $_cacheTag = 'askit_message';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'askit_message';

    /**
     * @var \Swissup\Askit\Model\ResourceModel\Answer\Collection\Factory
     */
    protected $answerCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Swissup\Askit\Model\ResourceModel\Answer\Collection\Factory $answerCollectionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $attributeValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Swissup\Askit\Model\ResourceModel\Answer\Collection\Factory $answerCollectionFactory,
        \Magento\Framework\Api\AttributeValueFactory $attributeValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->answerCollectionFactory = $answerCollectionFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Askit\Model\ResourceModel\Message');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get id
     *
     * return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get parent_id
     *
     * return int|null
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * Get item_type_id
     *
     * return int
     */
    public function getItemTypeId()
    {
        return $this->getData(ItemInterface::ITEM_TYPE_ID);
    }

    /**
     * Get item_id
     *
     * return int
     */
    public function getItemId()
    {
        return $this->getData(ItemInterface::ITEM_ID);
    }

    /**
     * Get store_id
     *
     * return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get customer_id
     *
     * return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Get customer_name
     *
     * return string
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Get email
     *
     * return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Get text
     *
     * return string
     */
    public function getText()
    {
        return $this->getData(self::TEXT);
    }

    /**
     * Get hint
     *
     * return int|null
     */
    public function getHint()
    {
        return $this->getData(self::HINT);
    }

    /**
     * Get status
     *
     * return bool|int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get created_time
     *
     * return string
     */
    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_TIME);
    }

    /**
     * Get update_time
     *
     * return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Get is_private
     *
     * return bool|int
     */
    public function getIsPrivate()
    {
        return $this->getData(self::IS_PRIVATE);
    }

    /**
     * Set id
     *
     * @param int $id
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set parent_id
     *
     * @param int $parentId
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Set item_type_id
     *
     * @param int $itemTypeId
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setItemTypeId($itemTypeId)
    {
        return $this->setData(ItemInterface::ITEM_TYPE_ID, $itemTypeId);
    }

    /**
     * Set item_id
     *
     * @param int $itemId
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(ItemInterface::ITEM_ID, $itemId);
    }

    /**
     * Set store_id
     *
     * @param int $storeId
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set customer_id
     *
     * @param int $customerId
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set customer_name
     *
     * @param string $customerName
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Set email
     *
     * @param string $email
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Set text
     *
     * @param string $text
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setText($text)
    {
        return $this->setData(self::TEXT, $text);
    }

    /**
     * Set hint
     *
     * @param int $hint
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setHint($hint)
    {
        return $this->setData(self::HINT, $hint);
    }

    /**
     * Set status
     *
     * @param int $status
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set created_time
     *
     * @param string $createdTime
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setCreatedTime($createdTime)
    {
        return $this->setData(self::CREATED_TIME, $createdTime);
    }

    /**
     * Set update_time
     *
     * @param string $updateTime
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set private
     *
     * @param int $isPrivate
     * return \Swissup\Askit\Api\Data\MessageInterface
     */
    public function setIsPrivate($isPrivate)
    {
        return $this->setData(self::IS_PRIVATE, $isPrivate);
    }

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Api\AttributeInterface|null null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES])
            && isset($this->_data[self::CUSTOM_ATTRIBUTES][$attributeCode])
                ? $this->_data[self::CUSTOM_ATTRIBUTES][$attributeCode]
                : null;
    }

    /**
     * Set an attribute value for a given attribute code
     *
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        /* If key corresponds to custom attribute code, populate custom attributes */
        if (in_array($attributeCode, $customAttributesCodes)) {
            /** @var \Magento\Framework\Api\AttributeValue $attribute */
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($attributeCode)
                ->setValue($attributeValue);
            $this->_data[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attribute;
        }
        return $this;
    }

    /**
     * Retrieve custom attributes values.
     *
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES]) ? $this->_data[self::CUSTOM_ATTRIBUTES] : [];
    }

    /**
     * Set array of custom attributes
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return $this
     * @throws \LogicException
     */
    public function setCustomAttributes(array $attributes)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof AttributeValue) {
                throw new \LogicException('Custom Attribute array elements can only be type of AttributeValue');
            }
            $attributeCode = $attribute->getAttributeCode();
            if (in_array($attributeCode, $customAttributesCodes)) {
                $this->_data[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attribute;
            }
        }
        return $this;
    }

    /**
     * Get a list of custom attribute codes.
     *
     * By default, entity can be extended only using extension attributes functionality.
     *
     * @return string[]
     */
    protected function getCustomAttributesCodes()
    {
        return isset($this->customAttributesCodes) ? $this->customAttributesCodes : [];
    }

    /**
     * Prepare post's statuses.
     * Available event blog_post_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getPrivateStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getQuestionStatuses()
    {
        return MessageStatus::getOptionArray();
    }

    public function getAnswerStatuses()
    {
        return MessageStatus::getOptionArray();
    }

    public function getEntityTypes()
    {
        return [
            self::TYPE_CATALOG_PRODUCT  => __('Product'),
            self::TYPE_CATALOG_CATEGORY => __('Category'),
            self::TYPE_CMS_PAGE         => __('Page')
        ];
    }

    public function getEntityTypeLabel($type = null)
    {
        if (empty($type)) {
            $type = $this->getItemTypeId();
        }
        $types = $this->getEntityTypes();

        return isset($types[$type]) ? $types[$type] : __('Product');
    }

    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Answer\Collection
     */
    public function getAnswerCollection()
    {
        $collection = $this->answerCollectionFactory->create();
        $collection->addParentIdFilter($this->getId());
        return $collection;
    }

    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Answer\Collection
     */
    public function getApprovedAnswerCollection()
    {
        $collection = $this->getAnswerCollection();
        $collection->addStatusFilter(MessageInterface::STATUS_APPROVED);
        return $collection;
    }

    /**
     * Retrieve array of product id's for message
     *
     * @return array
     */
    public function getAssignProducts()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('assign_products');
        if ($array === null) {
            $array = $this->getResource()->getAssignProducts($this);
            $this->setData('assign_products', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of cms page id's for message
     *
     * @return array
     */
    public function getAssignPages()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('assign_pages');
        if ($array === null) {
            $array = $this->getResource()->getAssignPages($this);
            $this->setData('assign_pages', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of cms page id's for message
     *
     * @return array
     */
    public function getAssignCategories()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('assign_categories');
        if ($array === null) {
            $array = $this->getResource()->getAssignCategories($this);
            $this->setData('assign_categories', $array);
        }
        return $array;
    }

    /**
     *
     * @return boolean
     */
    public function isQuestion()
    {
        return $this->getResource()->isQuestion($this);
    }

    /**
     *
     * @return boolean
     */
    public function isAdminAnswer()
    {
        return $this->getResource()->isAdminAnswer($this);
    }
}
