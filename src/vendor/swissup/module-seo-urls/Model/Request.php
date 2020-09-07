<?php

namespace Swissup\SeoUrls\Model;

class Request
{

    /**
     * @var \Swissup\SeoUrls\Helper\Filter
     */
    protected $filterHelper;

    /**
     * Constructor
     *
     * @param Filter $filterHelper
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Filter $filterHelper,
        \Swissup\SeoUrls\Model\Category $seoCategory
    ) {
        $this->filterHelper = $filterHelper;
        $this->seoCategory = $seoCategory;
    }

    /**
     * Get get paraneters in strings
     *
     * @param  string $queryString
     * @param  int $categoryId  [description]
     * @return array
     */
    public function getParamsFromString($queryString, $categoryId)
    {
        $params = [];
        $leftovers = [];
        $queryString = rawurldecode($queryString); // Decode URL-encoded strings
        $strings = explode('/', $queryString);
        foreach ($strings as $string) {
            $filterName = $this->filterHelper->findFilterInString($string);
            if ($filterName == $this->filterHelper->getPredefinedFilterRequestVar('category_filter')) {
                $valueLabel = str_replace(
                    $this->filterHelper->getPredefinedFilterLabel('category_filter') . '-',
                    '',
                    $string
                );
                $params[$filterName] = $this->findoutCategoryId($valueLabel, $categoryId);
            } elseif ($filterName) {
                $seoFilter = $this->filterHelper->getByName($filterName);
                $valueLabel = str_replace(
                    $seoFilter->getLabel().'-',
                    '',
                    $string
                );
                if ($seoFilter->getOptions()) {
                    // filter has options
                    $params[$filterName] = array_search(
                        $valueLabel,
                        $seoFilter->getOptions()
                    );
                    if ($params[$filterName] === false) {
                        // value not found
                        // perhaps there is some layered navigation to select multiple values
                        $value = '';
                        $options = $seoFilter->getOptions();
                        $iterations = 0; // prevent infinit loop
                        do {
                            $bestMatchLabel = '';
                            $bestMatchValue = '';
                            foreach ($options as $k => $l) {
                                if (strpos($valueLabel, $l) === 0 && $l > $bestMatchLabel) {
                                    $bestMatchLabel = $l;
                                    $bestMatchValue = $k;
                                }
                            }
                            if ($bestMatchLabel) {
                                $value .= $bestMatchValue . ',';
                                unset($options[$bestMatchValue]);
                                $valueLabel = substr($valueLabel, strlen($bestMatchLabel));
                                $valueLabel = ltrim($valueLabel, '-');
                            }
                            $iterations++;
                        } while (!empty($bestMatchLabel) && $iterations < 100);

                        $params[$filterName] = rtrim($value, ',');
                        $leftovers[$filterName] = $valueLabel;
                    }
                } else {
                    $params[$filterName] = $valueLabel;
                }
            }
        }

        return array_filter(
            $params + ['seo_leftovers' => array_filter($leftovers)]
        );
    }

    /**
     * Try to find out category id by its seo-label
     *
     * @param  string $categorySeoLabel
     * @param  int $categoryId ID of current category
     * @return
     */
    public function findoutCategoryId($categorySeoLabel, $categoryId)
    {
        if (!$categorySeoLabel) {
            return $categoryId;
        }

        $category = $this->filterHelper->getCategoryById($categoryId);
        return $this->getCategoryChildIdByLabel($category, $categorySeoLabel, '');

    }

    /**
     * Get ID of category by its SEO label
     *
     * @param  \Magento\Catalog\Api\Data\CategoryInterface $category
     * @param  string $valueLabel
     * @param  string $prefix
     * @return int|null
     */
    private function getCategoryChildIdByLabel($category, $valueLabel, $prefix)
    {
        $o = [];
        foreach ($category->getChildrenCategories() as $c) {
            $o[$prefix . $this->seoCategory->getStoreLabel($c)] = $c;
        }

        if (isset($o[$valueLabel])) {
            return $o[$valueLabel]->getId();
        }

        krsort($o);
        foreach ($o as $seoLabel => $c) {
            if (strpos($valueLabel, $seoLabel) === 0) {
                return $this->getCategoryChildIdByLabel($c, $valueLabel, $seoLabel . '-');
            }
        }

        return null;
    }

    public function mergeAndAppendValues(array $array1, array $array2)
    {
        foreach ($array2 as $key => $value) {
            if (isset($array1[$key])) {
                $_value = explode(',', $array1[$key]);
                $value  = explode(',', $value);
                $_value = array_merge($_value, $value);
                $_value = array_filter($_value);
                $_value = array_unique($_value);
                sort($_value);
                $value = implode(',', $_value);
            }
            $array1[$key] = $value;
        }

        return $array1;
    }
}
