<?php

namespace Gudoguy\DeprintersSync\Model;

use Gudoguy\Greeting\Helper\PrintDealHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;

class Category {
    const ROOT_CATEGORY_ID = 2;
    /**
     * @var CategoryFactory
     */
    public $categoryFactory;
    /**
     * @var CategoryRepositoryInterface
     */
    public $categoryRepository;
    /**
     * @var Collection
     */
    public $collection;

    /**
     * @var array
     */
    public $categoryItems;
    /**
     * @var PrintDealHelper
     */
    public $printDealHelper;

    public function __construct(
            CategoryFactory $categoryFactory,
            CategoryRepositoryInterface $categoryRepository,
            Collection $collection,
            PrintDealHelper $printDealHelper) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->collection = $collection;
        $this->printDealHelper = $printDealHelper;
    }

    public function categoryExist($url) {
        return isset($this->getCategoryItems()[$url]);
    }

    public function sync() {
        $added = 0;
        $deleted = 0;

        $categories = $this->printDealHelper->getCategories();
        $apiUrls = [];
        foreach ($categories as $category) {
            $name = $category['name'];
            $sku = $category['sku'];

            $url = $this->prepareUrl($name);
            $apiUrls[] = $url;

            if (!$this->categoryExist($url)) {
                $category = $this->categoryFactory->create();

                $category->setName($name)
                    ->setParentId(static::ROOT_CATEGORY_ID)
                    ->setIsActive(true)
                    ->setUrlKey($url)
                    ->setDeprintersSku($sku);

                try {
                    $this->categoryRepository->save($category);
                    $added++;
                } catch (CouldNotSaveExceptionon $ignored) {}
            }
        }


        $categoryItems = $this->getCategoryItems();
        $dbUrls = array_keys($categoryItems);
        $urlsDiff = array_diff($dbUrls, $apiUrls);

        foreach ($urlsDiff as $extraUrl) {
            break;
            if ($this->categoryRepository->delete($categoryItems[$extraUrl])) {
                $deleted++;
            }
        }

        print_r("Added: ${added} categories");
        print_r("Deleted: ${deleted} categories");
    }

    public function getCategoryItems() {
        if (!$this->categoryItems) {
            foreach ($this->collection as $category) {
                $this->categoryItems[$category->getUrlKey()] = $category;
            }
        }

        return $this->categoryItems;
    }

    public function prepareUrl($name) {
        return strtolower(str_replace(' ', '-',
            preg_replace("/[^\w\d\s]+/", "", $name)));
    }
}
