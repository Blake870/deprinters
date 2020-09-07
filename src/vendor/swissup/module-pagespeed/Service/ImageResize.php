<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Service;

use Magento\Catalog\Helper\Image as ImageHelper;
use Swissup\Pagespeed\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssertImageFactory;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Framework\App\State;
use Magento\Framework\View\ConfigInterface as ViewConfig;
// use \Magento\Catalog\Model\ResourceModel\Product\Image as ProductImage;
use Swissup\Pagespeed\Model\ResourceModel\Product\ImageGenerator as ProductImage;
use Magento\Theme\Model\Config\Customization as ThemeCustomizationConfig;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageResize
{
    /**
     * @var State
     */
    // private $appState;

    /**
     * @var MediaConfig
     */
    private $imageConfig;

    /**
     * @var ProductImage
     */
    private $productImage;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var ParamsBuilder
     */
    private $paramsBuilder;

    /**
     * @var ViewConfig
     */
    private $viewConfig;

    /**
     * @var AssertImageFactory
     */
    private $assertImageFactory;

    /**
     * @var ThemeCustomizationConfig
     */
    private $themeCustomizationConfig;

    /**
     * @var Collection
     */
    private $themeCollection;

    /**
     * @var Filesystem
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @url https://github.com/marc1706/fast-image-size
     *
     * @var \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface
     */
    private $imageSize;

    /**
     * Files collection object
     *
     * @var \Magento\Framework\Data\Collection\Filesystem
     */
    private $filesCollection;

    /**
     * Storage collection factory
     *
     * @var \Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory
     */
    protected $storageCollectionFactory;

    /**
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $configHelper;

    /**
     * @param State $appState
     * @param MediaConfig $imageConfig
     * @param ProductImage $productImage
     * @param ImageFactory $imageFactory
     * @param ParamsBuilder $paramsBuilder
     * @param ViewConfig $viewConfig
     * @param AssertImageFactory $assertImageFactory
     * @param ThemeCustomizationConfig $themeCustomizationConfig
     * @param Collection $themeCollection
     * @param Filesystem $filesystem
     * @param \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface $imageSize
     * @param \Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory $storageCollectionFactory
     * @param \Swissup\Pagespeed\Helper\Config $configHelper
     * @param ProductMetadataInterface  $productMetadata
     * @internal param ProductImage $gallery
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        State $appState,
        MediaConfig $imageConfig,
        ProductImage $productImage,
        ImageFactory $imageFactory,
        ParamsBuilder $paramsBuilder,
        ViewConfig $viewConfig,
        AssertImageFactory $assertImageFactory,
        ThemeCustomizationConfig $themeCustomizationConfig,
        Collection $themeCollection,
        Filesystem $filesystem,
        \Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface $imageSize,
        \Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory $storageCollectionFactory,
        \Swissup\Pagespeed\Helper\Config $configHelper,
        ProductMetadataInterface $productMetadata
    ) {
        // $this->appState = $appState;
        // $this->appState->setAreaCode(Area::AREA_GLOBAL);
        $this->imageConfig = $imageConfig;
        $this->productImage = $productImage;
        $this->imageFactory = $imageFactory;
        $this->paramsBuilder = $paramsBuilder;
        $this->viewConfig = $viewConfig;
        $this->assertImageFactory = $assertImageFactory;
        $this->themeCustomizationConfig = $themeCustomizationConfig;
        $this->themeCollection = $themeCollection;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->filesystem = $filesystem;
        $this->imageSize = $imageSize;
        $this->storageCollectionFactory = $storageCollectionFactory;
        $this->configHelper = $configHelper;
        $this->magentoMetadata = $productMetadata;
    }

    // /**
    //  * Create resized images of different sizes from an original image
    //  * @param string $originalImageName
    //  * @throws NotFoundException
    //  */
    // public function resizeFromImageName(string $originalImageName)
    // {
    //     $originalImagePath = $this->mediaDirectory->getAbsolutePath(
    //         $this->imageConfig->getMediaPath($originalImageName)
    //     );
    //     if (!$this->mediaDirectory->isFile($originalImagePath)) {
    //         throw new NotFoundException(__('Cannot resize image "%1" - original image not found', $originalImagePath));
    //     }
    //     foreach ($this->getViewImages($this->getThemesInUse()) as $viewImage) {
    //         $this->resize($viewImage, $originalImagePath, $originalImageName);
    //     }
    // }

    /**
     * Create resized images of different sizes from themes
     * @param array|null $themes
     * @return \Generator
     * @throws NotFoundException
     */
    public function resizeAllProductImages(array $themes = null): \Generator
    {
        $count = $this->productImage->getCountAllProductImages();
        if (!$count) {
            throw new NotFoundException(__('Cannot resize images - product images not found'));
        }

        $productImages = $this->productImage->getAllProductImages();
        $viewImages = $this->getViewImages($themes ?? $this->getThemesInUse());

        foreach ($productImages as $image) {
            $originalImageName = $image['filepath'];
            $originalImagePath = $this->mediaDirectory->getAbsolutePath(
                $this->imageConfig->getMediaPath($originalImageName)
            );
            if (file_exists($originalImagePath)) {
                foreach ($viewImages as $viewImage) {
                    $this->resize($viewImage, $originalImagePath, $originalImageName);
                }
            }

            yield $originalImageName => $count;
        }
    }

    /**
     *
     * @return \Generator
     * @throws NotFoundException
     */
    public function resizeCustomImages()
    {
        $viewImage = [
            'type' =>"image",
            'id' => "swissup_pagespeed_wysiwyg_default"
        ];
        $imageParams = $this->paramsBuilder->build($viewImage);

        $collection = $this->getCustomImagesCollection();
        $count = count($collection);
        foreach ($collection as $imageInfo) {
            $originalImagePath = $imageInfo['filename'];
            $dimensions = $this->imageSize->getDimensions($originalImagePath);
            if ($dimensions === false) {
                continue;
            }
            $imageParams['image_width'] = $dimensions['width'];
            $imageParams['image_height'] = $dimensions['height'];

            $originalImageName = $originalImagePath;

            if (file_exists($originalImagePath)) {
                $image = $this->makeImage($originalImagePath, $imageParams);
                $image->resize($imageParams['image_width'], $imageParams['image_height']);
                $image->save($originalImagePath);
                unset($image);

                $this->scale($imageInfo['filename'], $originalImagePath, $imageParams, [0.5, 0.75]);
            }

            yield $originalImageName => $count;
        }
    }

    /**
     * Prepared image collection for some directories
     *
     * @return \Magento\Framework\Data\Collection\Filesystem
     */
    public function getCustomImagesCollection()
    {
        if (!$this->filesCollection) {
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
            /** @var \Magento\Cms\Model\Wysiwyg\Images\Storage\Collection $collection */
            $collection = $this->storageCollectionFactory->create();

            $targetDirs = $this->configHelper->getResizeCommandTargetDirs();
            foreach ($targetDirs as $targetDir) {
                $path = $this->mediaDirectory->getAbsolutePath($targetDir);
                if ($path !== null && is_dir($path)) {
                    $collection->addTargetDir($path);
                }
            }
            $collection
                ->setCollectDirs(false)
                ->setCollectFiles(true)
                ->setCollectRecursively(true)
                ->setDirsFilter('/^(?!.*(0\.5x|0\.75x|2x|3x).*$)(.*)/i')
                ->setFilesFilter('/\.(' . implode('|', $allowedExtensions) . ')$/i')
                ->setOrder(
                    'mtime',
                    \Magento\Framework\Data\Collection::SORT_ORDER_ASC
                );
            $collection->load();

            $collectionRootMediaFiles = $this->storageCollectionFactory->create();
            $targetDir = '.';
            $path = $this->mediaDirectory->getAbsolutePath($targetDir);
            if ($path !== null && is_dir($path)) {
                $collectionRootMediaFiles->addTargetDir($path);
                $collectionRootMediaFiles
                    ->setCollectDirs(false)
                    ->setCollectFiles(true)
                    ->setCollectRecursively(false)
                    ->setFilesFilter('/\.(' . implode('|', $allowedExtensions) . ')$/i')
                    ->setOrder('mtime', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
                ;
                if ($collectionRootMediaFiles->getSize() > 0) {
                    $lastId = $collection->getLastItem()->getId();
                    foreach ($collectionRootMediaFiles as $item) {
                        $lastId += 1;
                        $item->setId($lastId);
                        $collection->addItem($item);
                    }
                }
            }

            $this->filesCollection = $collection;
        }

        return $this->filesCollection;
    }

    /**
     * Search the current theme
     * @return array
     */
    private function getThemesInUse(): array
    {
        $themesInUse = [];
        $registeredThemes = $this->themeCollection->loadRegisteredThemes();
        $storesByThemes = $this->themeCustomizationConfig->getStoresByThemes();
        $keyType = is_integer(key($storesByThemes)) ? 'getId' : 'getCode';
        foreach ($registeredThemes as $registeredTheme) {
            if (array_key_exists($registeredTheme->$keyType(), $storesByThemes)) {
                $themesInUse[] = $registeredTheme;
            }
        }
        return $themesInUse;
    }

    /**
     * Get view images data from themes
     * @param array $themes
     * @return array
     */
    private function getViewImages(array $themes): array
    {
        $viewImages = [];
        /** @var \Magento\Theme\Model\Theme $theme */
        foreach ($themes as $theme) {
            $config = $this->viewConfig->getViewConfig([
                'area' => Area::AREA_FRONTEND,
                'themeModel' => $theme,
            ]);
            $images = $config->getMediaEntities('Magento_Catalog', ImageHelper::MEDIA_TYPE_CONFIG_NODE);
            foreach ($images as $imageId => $imageData) {
                $uniqIndex = $this->getUniqueImageIndex($imageData);
                $imageData['id'] = $imageId;
                $viewImages[$uniqIndex] = $imageData;
            }
        }
        return $viewImages;
    }

    /**
     * Get unique image index
     * @param array $imageData
     * @return string
     */
    private function getUniqueImageIndex(array $imageData): string
    {
        ksort($imageData);
        unset($imageData['type']);
        return hash('md5', json_encode($imageData));
    }

    /**
     * Make image
     * @param string $originalImagePath
     * @param array $imageParams
     * @return Image
     */
    private function makeImage(string $originalImagePath, array $imageParams): Image
    {
        $image = $this->imageFactory->create($originalImagePath);
        $image->keepAspectRatio($imageParams['keep_aspect_ratio']);
        $image->keepFrame($imageParams['keep_frame']);
        $image->keepTransparency($imageParams['keep_transparency']);
        $image->constrainOnly($imageParams['constrain_only']);
        $image->backgroundColor($imageParams['background']);
        $image->quality($imageParams['quality']);
        return $image;
    }

    /**
     * Resize image
     * @param array $viewImage
     * @param string $originalImagePath
     * @param string $originalImageName
     */
    private function resize(array $viewImage, string $originalImagePath, string $originalImageName)
    {
        $imageParams = $this->paramsBuilder->build($viewImage);

        $image = $this->makeImage($originalImagePath, $imageParams);
        $imageAsset = $this->assertImageFactory->create(
            [
                'miscParams' => $this->convertToPatchedFormat($imageParams),
                'filePath' => $originalImageName,
            ]
        );

        if (!empty($imageParams['image_width']) && !empty($imageParams['image_height'])) {
            $image->resize($imageParams['image_width'], $imageParams['image_height']);
        } else {
            $dimensions = $this->imageSize->getDimensions($imageAsset->getPath());
            if ($dimensions === false) {
                return;
            }
            $imageParams['image_width'] = $dimensions['width'];
            $imageParams['image_height'] = $dimensions['height'];
        }
        $image->save($imageAsset->getPath());
        // print_r($imageAsset->getPath() . '  ' . $imageParams['image_width'] . 'x' . $imageParams['image_height']);

        if (!empty($imageParams['image_width']) && !empty($imageParams['image_height'])) {
            $path = $imageAsset->getPath();
            $this->scale($path, $originalImagePath, $imageParams);
        }

        // print_r($imageAsset->getFilePath());
        // print_r(' ---> ' . $imageAsset->getPath());
        // // print_r($imageAsset->getImageInfo());
        // echo "                       \n";
    }

    /**
     * @param $miscParams
     * @return array
     */
    private function convertToPatchedFormat($miscParams)
    {
        $return = [];

        $keys = [
            'image_height',
            'image_width',
            'quality',
            'angle',
            'keep_aspect_ratio',
            'keep_frame',
            'keep_transparency',
            'constrain_only',
        ];

        foreach ($keys as $key) {
            if (isset($miscParams[$key])) {
                $return[$key] = $miscParams[$key];
            }
        }

        if (isset($miscParams['background'])) {
            $background = $miscParams['background'];
            if (is_array($background)
                && version_compare($this->magentoMetadata->getVersion(), '2.3.0', '<')
            ) {
                $background = 'rgb' . implode(',', $background);
            }
            $return['background'] = $background;
        }

        return $return;
    }

    /**
     *
     * @param  string $path
     * @param  string $originalImagePath
     * @param  array $imageParams
     * @param  array $resolutions
     * @return void
     */
    private function scale(string $path, string $originalImagePath, $imageParams, $resolutions = [0.5, 0.75,/*1,*/ 2, 3])
    {
        $originalImageWidth = $imageParams['image_width'];
        $originalImageHeight = $imageParams['image_height'];
        $basename = basename($path);

        foreach ($resolutions as $resolution) {
            $width = ceil($originalImageWidth * $resolution);
            $height = ceil($originalImageHeight * $resolution);

            $imageParams['image_width'] = $width;
            $imageParams['image_height'] = $height;
            $image = $this->makeImage($originalImagePath, $imageParams);
            $image->resize($width, $height);

            $savePath = str_replace($basename, $resolution . 'x' . DIRECTORY_SEPARATOR . $basename, $path);
            // echo "                       \n";
            // print_r($savePath . '  ' . $width . 'x' . $height);
            $image->save($savePath);
            unset($image);
        }
    }
}
