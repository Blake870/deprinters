<?php

namespace Swissup\Ajaxsearch\Test\Unit\Model\Query\Cms;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
// use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class PageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Query
     */
    private $model;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    /**
     * @var \Magento\Search\Model\ResourceModel\Query|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;


    private $collectionMock;

    /**
     * @var \Swissup\Ajaxsearch\Model\Query\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryCollectionFactoryMock;

    /**
     *  @var \Swissup\Ajaxsearch\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configHelperMock;

    /**
     * @var [type]
     */
    private $filterPoolMock;

    /**
     * @var Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilderMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);

        $this->resourceMock = $this->getMockBuilder(\Magento\Search\Model\ResourceModel\Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionMock = $this->getMockBuilder(
            \Magento\Cms\Model\ResourceModel\Page\Grid\Collection::class
        )
            ->setMethods([
                'getIterator',
                'setPageSize',
                'addStoreFilter'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        /* \Swissup\Ajaxsearch\Model\Query\CollectionFactory::class */
        $this->queryCollectionFactoryMock =  $this
            ->getMockBuilder(\Magento\Search\Model\ResourceModel\Query\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'setInstanceName'])
            ->getMock();

        $this->queryCollectionFactoryMock =  $this
            ->getMockBuilder(\Swissup\Ajaxsearch\Model\Query\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'setInstanceName'])
            ->getMock();

        $this->configHelperMock = $this->getMockBuilder(\Swissup\Ajaxsearch\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterPoolMock = $this
            ->getMockBuilder(\Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['applyFilters'])
            ->getMock();

        $this->searchCriteriaBuilderMock = $this
            ->getMockBuilder(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'addFilter'])
            ->getMock();

        $this->searchCriteriaMock = $this
            ->getMockBuilder(\Magento\Framework\Api\Search\SearchCriteria::class)
            ->disableOriginalConstructor()
            ->setMethods(['setRequestName'])
            ->getMock();

        $this->filterBuilderMock = $this
            ->getMockBuilder(\Magento\Framework\Api\FilterBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setConditionType', 'setField', 'setValue', 'create'])
            ->getMock();

        $queryString = 'contact';

        $this->storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            \Swissup\Ajaxsearch\Model\Query\Cms\Page::class,
            [
                'resource' => $this->resourceMock,
                'queryCollectionFactory' => $this->queryCollectionFactoryMock,
                'configHelper' => $this->configHelperMock,
                'filterPool' => $this->filterPoolMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
                'data' => [
                    'query_text' => $queryString
                ],
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    public function testGetSuggestCollection()
    {
        $this->queryCollectionFactoryMock->expects($this->once())
            ->method('setInstanceName')
            ->with(\Magento\Cms\Model\ResourceModel\Page\Grid\Collection::class)
            ->willReturnSelf();

        $this->queryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $limit = 10;
        $this->configHelperMock->expects($this->once())
            ->method('getPageLimit')
            ->willReturn($limit);

        $this->collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($limit)
            ->willReturnSelf();

        $storeId = 1;

        $storeMock = $this->getMockBuilder(
            \Magento\Store\Model\Store::class
        )->disableOriginalConstructor()->setMethods(
            ['getId']
        )->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->collectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->with($storeId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->searchCriteriaMock->expects($this->once())
            ->method('setRequestName')
            ->with('cms_page_listing_data_source')
            ->willReturnSelf();

        $this->filterBuilderMock->expects($this->once())
            ->method('setConditionType')
            ->with('fulltext')
            ->willReturnSelf();

        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('fulltext')
            ->willReturnSelf();

        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with('contact')
            ->willReturnSelf();

        $filterMock = $this
            ->getMockBuilder(\Magento\Framework\Api\Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $this->filterPoolMock->expects($this->once())
            ->method('applyFilters')
            ;

        $collection = $this->model->getSuggestCollection();
        // $this->assertEquals([], $result);
    }
}
