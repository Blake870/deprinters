<?php
namespace Swissup\Askit\Plugin\Model\Category;

use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\CategoryFactory;

use Swissup\Askit\Api\Data\MessageInterface;

class DataProvider
{
    const GROUP_ASKIT   = 'askit';
    const GROUP_CONTENT = 'content';
    const SORT_ORDER    = 120;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        CategoryFactory $categoryFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->request = $request;
        $this->categoryFactory = $categoryFactory;
    }

    public function afterGetMeta(
        \Magento\Catalog\Model\Category\DataProvider $subject,
        $result
    ) {
        $result['askit'] = [
            'children' => [
                'askit_question_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'askit_question_listing',
                                'externalProvider' => 'askit_question_listing.askit_question_listing_data_source',
                                'selectionsProvider' => 'askit_question_listing.askit_question_listing.askit_message_columns.ids',
                                'ns' => 'askit_question_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render', ['current_category_id' => $this->getCurrentCategoryId()] /* for massaction filter */),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => false,
                                'currentCategoryId' => $this->getCurrentCategoryId(),
                                'itemTypeId' => MessageInterface::TYPE_CATALOG_CATEGORY,
                                'exports' => [
                                    'currentCategoryId' => '${ $.externalProvider }:params.current_category_id',
                                    'itemTypeId' => '${ $.externalProvider }:params.item_type_id'
                                ]
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Questions'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' => static::SORT_ORDER
                    ],
                ],
            ],
        ];
        return $result;
    }

    /**
     * Get current category id
     *
     * @return int
     */
    protected function getCurrentCategoryId()
    {
        $category = $this->registry->registry('category');
        if ($category) {
            return $category->getId();
        }
        $requestId = $this->request->getParam('id');
        $requestScope = $this->request->getParam('store', Store::DEFAULT_STORE_ID);
        if ($requestId) {
            $category = $this->categoryFactory->create();
            $category->setStoreId($requestScope);
            $category->load($requestId);
            // if (!$category->getId()) {
            //     return;
            //     // throw NoSuchEntityException::singleField('id', $requestId);
            // }
        }
        return $category->getId();
    }
}
