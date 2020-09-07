<?php
namespace Swissup\Attributepages\Model;

use Swissup\Attributepages\Api\Data\EntityInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Entity extends \Magento\Framework\Model\AbstractModel
    implements EntityInterface, IdentityInterface
{
    /**
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const DISPLAY_MODE_MIXED       = 'mixed';
    const DISPLAY_MODE_DESCRIPTION = 'description';
    const DISPLAY_MODE_CHILDREN    = 'children';

    const LISTING_MODE_IMAGE = 'image';
    const LISTING_MODE_LINK  = 'link';
    const LISTING_MODE_GRID  = 'grid';
    const LISTING_MODE_LIST  = 'list';

    const DELIMITER = ',';

    const IMAGE_PATH = 'swissup/attributepages';
    /**
     * cache tag
     */
    const CACHE_TAG = 'attributepages_entity';

    /**
     * @var string
     */
    protected $_cacheTag = 'attributepages_entity';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'attributepages_entity';
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attrCollectionFactory;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $attrOptionCollectionFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $coreResource;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * URL instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory
     */
    protected $attrpagesCollectionFactory;
    /**
     * @var \Swissup\Attributepages\Helper\Product
     */
    protected $attrpagesProductHelper;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $coreResource
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory
     * @param \Swissup\Attributepages\Helper\Product $attrpagesProductHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory,
        \Swissup\Attributepages\Helper\Product $attrpagesProductHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->attrCollectionFactory = $attrCollectionFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->coreResource = $coreResource;
        $this->jsonHelper = $jsonHelper;
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->attrpagesCollectionFactory = $attrpagesCollectionFactory;
        $this->attrpagesProductHelper = $attrpagesProductHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Attributepages\Model\ResourceModel\Entity');
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
     * Get entity_id
     *
     * return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Get attribute_id
     *
     * return int
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * Get option_id
     *
     * return int
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * Get name
     *
     * return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get identifier
     *
     * return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Get title
     *
     * return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get page_title
     *
     * return string
     */
    public function getPageTitle()
    {
        $pageTitle = $this->getData(self::PAGE_TITLE);
        if (empty($pageTitle)) {
            $pageTitle = $this->getTitle();
        }
        return $pageTitle;
    }

    /**
     * Get content
     *
     * return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Get image
     *
     * return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get thumbnail
     *
     * return string
     */
    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * Get meta_keywords
     *
     * return string
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * Get meta_description
     *
     * return string
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * Get display_settings
     *
     * return string
     */
    public function getDisplaySettings()
    {
        return $this->getData(self::DISPLAY_SETTINGS);
    }

    /**
     * Get root_template
     *
     * return string
     */
    public function getRootTemplate()
    {
        return $this->getData(self::ROOT_TEMPLATE);
    }

    /**
     * Get layout_update_xml
     *
     * return string
     */
    public function getLayoutUpdateXml()
    {
        return $this->getData(self::LAYOUT_UPDATE_XML);
    }

    /**
     * Get use_for_attribute_page
     *
     * return int
     */
    public function getUseForAttributePage()
    {
        return $this->getData(self::USE_FOR_ATTRIBUTE_PAGE);
    }

    /**
     * Get use_for_product_page
     *
     * return int
     */
    public function getUseForProductPage()
    {
        return $this->getData(self::USE_FOR_PRODUCT_PAGE);
    }

    /**
     * Get excluded_option_ids
     *
     * return string
     */
    public function getExcludedOptionIds()
    {
        return $this->getData(self::EXCLUDED_OPTION_IDS);
    }

    /**
     * Set entity_id
     *
     * @param int $entityId
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Set attribute_id
     *
     * @param int $attributeId
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setAttributeId($attributeId)
    {
        return $this->setData(self::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * Set option_id
     *
     * @param int $optionId
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * Set name
     *
     * @param string $name
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set title
     *
     * @param string $title
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set page_title
     *
     * @param string $pageTitle
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setPageTitle($pageTitle)
    {
        return $this->setData(self::PAGE_TITLE, $pageTitle);
    }

    /**
     * Set content
     *
     * @param string $content
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set image
     *
     * @param string $image
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * Set meta_keywords
     *
     * @param string $metaKeywords
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Set meta_description
     *
     * @param string $metaDescription
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set display_settings
     *
     * @param string $displaySettings
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setDisplaySettings($displaySettings)
    {
        return $this->setData(self::DISPLAY_SETTINGS, $displaySettings);
    }

    /**
     * Set root_template
     *
     * @param string $rootTemplate
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setRootTemplate($rootTemplate)
    {
        return $this->setData(self::ROOT_TEMPLATE, $rootTemplate);
    }

    /**
     * Set layout_update_xml
     *
     * @param string $layoutUpdateXml
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * Set use_for_attribute_page
     *
     * @param int $useForAttributePage
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setUseForAttributePage($useForAttributePage)
    {
        return $this->setData(self::USE_FOR_ATTRIBUTE_PAGE, $useForAttributePage);
    }

    /**
     * Set use_for_product_page
     *
     * @param int $useForProductPage
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setUseForProductPage($useForProductPage)
    {
        return $this->setData(self::USE_FOR_PRODUCT_PAGE, $useForProductPage);
    }

    /**
     * Set excluded_option_ids
     *
     * @param string $excludedOptionIds
     * return \Swissup\Attributepages\Api\Data\EntityInterface
     */
    public function setExcludedOptionIds($excludedOptionIds)
    {
        return $this->setData(self::EXCLUDED_OPTION_IDS, $excludedOptionIds);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Prepare page's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
     /**
     * Entity could be the page or option for page
     *
     * @return boolean
     */
    public function isAttributeBasedPage()
    {
        return !(bool)$this->getOptionId();
    }
    /**
     * Entity could be the page or option for page
     *
     * @return boolean
     */
    public function isOptionBasedPage()
    {
        return (bool)$this->getOptionId();
    }
    /**
     * Retrieve attribute object
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute()
    {
        $attribute = $this->_getData('attribute');
        if (!$attribute && $this->getAttributeId()) {
            $attribute = $this->attrCollectionFactory->create()
                ->addFieldToFilter('main_table.attribute_id', $this->getAttributeId())
                ->getFirstItem();
            if ($attribute) {
                $this->setData('attribute', $attribute);
            }
        }
        return $this->_getData('attribute');
    }
    /**
     * Retrieve related options. Callable on attribute based page only.
     *
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    public function getRelatedOptions()
    {
        $options = $this->_getData('related_options');
        if (!$options && $this->isAttributeBasedPage()) {
            $options = $this->attrOptionCollectionFactory->create()
                ->setAttributeFilter($this->getAttributeId());
            $table = $this->coreResource->getTableName('eav_attribute_option_value');
            $options->getSelect()
                ->joinLeft(
                    ['sort_alpha_value' => $table],
                    'sort_alpha_value.option_id = main_table.option_id AND sort_alpha_value.store_id = 0',
                    ['value']
                );
        }
        return $options;
    }

    public function getExcludedOptionIdsArray()
    {
        $ids = $this->getExcludedOptionIds();
        if (!$ids) {
            $ids = [];
        } else if (!is_array($ids)) {
            $ids = explode(self::DELIMITER, $ids);
        }
        return $ids;
    }

    public function importOptionData($option, $applyDefaults = true)
    {
        $this->setAttributeId($option->getAttributeId())
            ->setOptionId($option->getOptionId())
            ->setTitle($option->getValue())
            ->setName($option->getValue());
        $identifier = $option->getValue();
        if (function_exists('mb_strtolower')) {
            $identifier = mb_strtolower($identifier, 'UTF-8');
        }
        $this->setIdentifier($identifier);
        if ($applyDefaults) {
            $this->setDisplayMode(self::DISPLAY_MODE_MIXED)
                ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
        }
        return $this;
    }
    /**
     * Overriden to convert the json saved display settings to array style
     *
     * @param string $key
     * @param mixed $value
     * @return TM_Attributepages_Model_Entity
     */
    public function setData($key, $value = null)
    {
        parent::setData($key, $value);
        if ((is_array($key) && array_key_exists('display_settings', $key))
            || 'display_settings' === $key) {
            if (is_array($key)) {
                $value = $key['display_settings'];
            }
            try {
                $config = $this->jsonHelper->jsonDecode($value);
            } catch (\Exception $e) {
                $config = [];
            }
            foreach ($config as $key => $value) {
                parent::setData($key, $value);
            }
        }
        return $this;
    }
    /**
     * Retrieve page url
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->getIdentifier();
        if ($parent = $this->getParentPage()) {
            $url = $parent->getIdentifier() . '/' . $url;
        }
        return $this->url->getUrl($url);
    }
    /**
     * Retrieve parent page for current entity
     *
     * @return mixed
     */
    public function getParentPage()
    {
        if ($this->isAttributeBasedPage()) {
            return false;
        }
        $parentPage = $this->getData('parent_page');
        if (null === $parentPage) {
            $storeId = $this->storeManager->getStore()->getId();
            $collection = $this->attrpagesCollectionFactory->create()
                ->addAttributeOnlyFilter()
                ->addFieldToFilter('attribute_id', $this->getAttributeId())
                ->addUseForAttributePageFilter() // enabled flag
                ->addStoreFilter($storeId);
            if ($identifier = $this->getParentPageIdentifier()) {
                $collection->addFieldToFilter('identifier', $identifier);
            }
            $parentPage = $this->attrpagesProductHelper->findParentPage(
                $this, $collection, $storeId, $this->getParentPageIdentifier()
            );
            $this->setData('parent_page', $parentPage);
        }
        return $parentPage;
    }
    /**
     * Retreive option object
     *
     */
    public function getOption()
    {
        $option = $this->_getData('option');
        if (!$option && $this->getOptionId()) {
            $collection = $this->attrOptionCollectionFactory->create()
                ->addFieldToFilter('main_table.option_id', $this->getOptionId());
            $collection->getSelect()
                ->joinLeft(
                    ['sort_alpha_value' => $this->coreResource->getTableName('eav_attribute_option_value')],
                    'sort_alpha_value.option_id = main_table.option_id AND sort_alpha_value.store_id = 0',
                    ['value']
                );
            $option = $collection->getFirstItem();
            if ($option) {
                $this->setData('option', $option);
            }
        }
        return $this->_getData('option');
    }
    public function isMixedMode()
    {
        return $this->getDisplayMode() == self::DISPLAY_MODE_MIXED;
    }
    public function isDescriptionMode()
    {
        return $this->getDisplayMode() == self::DISPLAY_MODE_DESCRIPTION;
    }
    public function isChildrenMode()
    {
        return $this->getDisplayMode() == self::DISPLAY_MODE_CHILDREN;
    }
}
