<?php

namespace Swissup\SeoImages\Plugin\Model\View\Asset;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Swissup\SeoImages\Model\ResourceModel\Product\Gallery as ProductGallery;
use Magento\Framework\App\ProductMetadataInterface;

class Image
{
    /**
     * @var ProductRepository;
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProductGallery
     */
    private $gallery;

    /**
     * @var \Swissup\SeoImages\Model\NameGenerator
     */
    private $fileName;

    /**
     * @var \Swissup\SeoImages\Helper\Data
     */
    private $helper;

    /**
     * @var ProductMetadataInterface
     */
    private $magentoMetadata;

    /**
     * @var string[]
     */
    private $targetFiles = [];

    /**
     * @param ProductRepository                      $productRepository
     * @param SearchCriteriaBuilder                  $searchCriteriaBuilder
     * @param ProductGallery                         $gallery
     * @param \Swissup\SeoImages\Model\NameGenerator $fileName
     * @param \Swissup\SeoImages\Helper\Data         $helper
     * @param ProductMetadataInterface               $magentoMetadata
     */
    public function __construct(
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Swissup\SeoImages\Model\ResourceModel\Product\Gallery $gallery,
        \Swissup\SeoImages\Model\NameGenerator $fileName,
        \Swissup\SeoImages\Helper\Data $helper,
        ProductMetadataInterface $magentoMetadata
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->gallery = $gallery;
        $this->fileName = $fileName;
        $this->helper = $helper;
        $this->magentoMetadata = $magentoMetadata;
    }

    /**
     * After plugin.
     * Change destination path to resized image on server.
     *
     * @param  \Magento\Catalog\Model\View\Asset\Image $subject
     * @param  string                                  $result
     * @return string
     */
    public function afterGetPath(
        \Magento\Catalog\Model\View\Asset\Image $subject,
        $result
    ) {
        if (!$this->helper->canChangeName()) {
            return $result;
        }

        $originalFile = $subject->getFilePath();
        $targetFile = $this->getTargetFileName($originalFile);
        if (!$targetFile) {
            return $result;
        }

        $newPath = str_replace($originalFile, $targetFile, $result);

        return $newPath;
    }

    /**
     * Aftre plugin.
     * Change URL to resized image.
     *
     * @param  \Magento\Catalog\Model\View\Asset\Image $subject
     * @param  string                                  $result
     * @return string
     */
    public function afterGetUrl(
        \Magento\Catalog\Model\View\Asset\Image $subject,
        $result
    ) {
        if (!$this->helper->canChangeName()) {
            return $result;
        }

        $originalFile = $subject->getFilePath();
        $targetFile = $this->getTargetFileName($originalFile);
        if (!$targetFile) {
            return $result;
        }

        $newUrl = str_replace($originalFile, $targetFile, $result);
        $fileKey = $this->helper->buildFileKey($newUrl);
        $this->helper->saveSeoImage($fileKey, $originalFile, $targetFile);

        return $newUrl;
    }

    /**
     * Get new name for original file.
     *
     * @param  string $key
     * @return string
     */
    private function getTargetFileName($key)
    {
        if (!isset($this->targetFiles[$key])) {
            $originalFile = $key;
            $product = $this->findProduct($originalFile);
            $this->targetFiles[$key] = $product
                ? $this->fileName->generate($product,$originalFile)
                : '';
        }

        return $this->targetFiles[$key];
    }

    /**
     * Find product by gallery image assigned to it.
     *
     * @param  string                                          $galleryImage
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function findProduct($galleryImage)
    {
        $ids = $this->gallery->getProductIds($galleryImage);
        if (empty($ids)) {
            return null;
        }

        $criteria = $this->searchCriteriaBuilder
            ->addFilter($this->getFilterFiledName(), implode(',', $ids), 'in')
            ->create();

        $products = $this->productRepository->getList($criteria)->getItems();
        foreach ($products as $product) {
            if ($product->isVisibleInSiteVisibility()) {
                break;
            }
        }

        return $product ?? null;
    }

    /**
     * @return string
     */
    private function getFilterFiledName()
    {
        return $this->magentoMetadata->getEdition() === 'Enterprise'
            ? 'row_id'
            : 'entity_id';

    }
}
