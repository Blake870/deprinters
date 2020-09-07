<?php

namespace Swissup\SeoUrls\Model\Filter;

class Category extends AbstractFilter
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Catalog\Api\Data\CategoryInterface
     */
    protected $currentCategory;
    /**
     * @var integer
     */
    private $iterationCounter = 0;

    /**
     * @var \Swissup\SeoUrls\Model\Category
     */
    protected $seoCategory;

    /**
     * @param \Magento\Framework\Registry     $registry
     * @param \Swissup\SeoUrls\Helper\Data    $helper
     * @param \Swissup\SeoUrls\Model\Category $seoCategory
     * @param array                           $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Swissup\SeoUrls\Helper\Data $helper,
        \Swissup\SeoUrls\Model\Category $seoCategory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->seoCategory = $seoCategory;
        parent::__construct($helper, $data);
    }

    /**
     * Get category for layered filter
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategory()
    {
        if ($this->registry->registry('current_category_filter')) {
            return $this->registry->registry('current_category_filter');
        }

        return $this->getCurrentCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->helper->getPredefinedFilterLabel('category_filter');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $category = $this->getCategory();
        if ((null !== $category)
            && !$this->hasData("options_{$category->getId()}")
        ) {
            $options = [];
            foreach ($this->getCategory()->getChildrenCategories() as $child) {
                if (!$child->getIsActive()) {
                    continue;
                }

                $label = $this->seoCategory->getStoreLabel($child);
                if (in_array($label, $options)) {
                    // this should not occur - poor category naming
                    // concatenate value to duplicated label
                    $label .= '-' . $child->getId();
                }

                // option prefix is path to current category filter from current category
                $options[$child->getId()] = $this->getOptionPrefix() . $label;
            }
            // add current category filter option
            $options[$this->getCategory()->getId()] = rtrim($this->getOptionPrefix(), '-');
            // add parent of current category filter option
            $options[$this->getCategory()->getParentId()] = rtrim(
                $this->generateLabelForCategory(
                    '',
                    $this->getCategory()->getParentCategory()
                ),
                '-'
            );

            $this->setData("options_{$category->getId()}", $options);
        }

        return $this->getData("options_{$category->getId()}");
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return 0;
    }

    /**
     * Get prefix for filter options
     *
     * @return string
     */
    protected function getOptionPrefix()
    {
        $category = $this->getCategory();
        if (!$this->hasData("option_prefix_{$category->getId()}")) {
            $this->setData(
                "option_prefix_{$category->getId()}",
                $this->generateLabelForCategory()
            );
        }

        return $this->getData("option_prefix_{$category->getId()}");
    }

    /**
     * Generate label for category
     *
     * @param string $sufix
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return string
     * @throws \Exception
     */
    private function generateLabelForCategory($sufix = '', $category = null)
    {
        if ($category === null) {
            $category = $this->getCategory();
        }

        if ($category->getId() == $this->getCurrentCategory()->getId()
            || !$category->getParentId()
        ) {
            $this->iterationCounter = 0;
            return $sufix;
        } else {
            $sufix = $this->seoCategory->getStoreLabel($category)
                . '-'
                . $sufix
                ;
            if ($this->iterationCounter++ > 100) {
                throw new \Exception(__('Infinite recursion call or category tree is too deep.'));
            } else {
                return $this->generateLabelForCategory($sufix, $category->getParentCategory());
            }
        }
    }

    /**
     * Get curent category if it is set otherwise get root category
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    protected function getCurrentCategory()
    {

        if (!isset($this->currentCategory)) {
            if ($this->registry->registry('current_category')) {
                $this->currentCategory = $this->registry->registry('current_category');
            } else {
                $this->currentCategory = $this->helper->getRootCategory();
            }
        }

        return $this->currentCategory;
    }
}
