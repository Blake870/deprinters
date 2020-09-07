<?php
/**
 * Slide form gallery content
 *
 * @method \Magento\Framework\Data\Form\Element\AbstractElement getElement()
 */
namespace Swissup\EasySlide\Block\Adminhtml\Slider\Helper\Form\Gallery;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Swissup\EasySlide\Model\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;


class SlideContent extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'helper/gallery.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $imageUploadConfigDataProvider = ObjectManager::getInstance()
            ->get(ImageUploadConfigDataProvider::class);
        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            [
                'image_upload_config_data' => $imageUploadConfigDataProvider
            ]
        );
        $url = $this->_urlBuilder->addSessionParam()->getUrl(
            'easyslide/slider/upload');
        $this->getUploader()->getConfig()->setUrl(
            $url
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return $this;
    }

    public function getMediaAttributes()
    {
        return [];
    }

    public function getImagesJson()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $slidesModel = $_objectManager->create('Swissup\EasySlide\Model\Slides');
        $mediaDirectory = $_objectManager->create('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $elementPath = "easyslide";
        $path = $mediaDirectory->getAbsolutePath() . $elementPath;
        $request = $_objectManager->get('Magento\Framework\App\Request\Http');
        $sliderId = $request->getParam('slider_id');
        $slides = $slidesModel->getSlides($sliderId);

        $result = [];
        foreach ($slides as $slide) {
            $mediaUrl = $_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $mediaUrl .= $elementPath . "/" . $slide['image'];

            $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $fileHandler['size'] = 0;
            if (is_file($path . '/' . $slide['image'])) {
                $fileHandler = $directory->stat('easyslide/' . $slide['image']);
            }

            $result[] = [
                'slide_id' => $slide['slide_id'],
                'file' => $slide['image'],
                'url' => $mediaUrl,
                'position' => $slide['sort_order'],
                'title' => $slide['title'],
                'link' => $slide['url'],
                'target' => $slide['target'],
                'description' => $slide['description'],
                'desc_position' => $slide['desc_position'],
                'desc_background' => $slide['desc_background'],
                'size' => $fileHandler['size'],
                'is_active' => $slide['is_active']
            ];
        }

        return $this->_jsonEncoder->encode($result);
    }

    public function getDescPosValues()
    {
        return [
            "0" => [
                "value" => "top",
                "label" => "top"
            ],
            "1" => [
                "value" => "right",
                "label" => "right"
            ],
            "2" => [
                "value" => "bottom",
                "label" => "bottom"
            ],
            "3" => [
                "value" => "left",
                "label" => "left"
            ]
        ];
    }

    public function getDescBackValues()
    {
        return [
            "0" => ["value" => "light", "label" => "light"],
            "1" => ["value" => "dark", "label" => "dark"],
            "2" => ["value" => "transparent", "label" => "transparent"]
        ];
    }

    public function getTargetValues()
    {
        return [
            "0" => ["value" => "_self", "label" => "Same window"],
            "1" => ["value" => "_blank", "label" => "New window"]
        ];
    }

    public function getActiveValues()
    {
        return [
            "0" => ["value" => 0, "label" => "No"],
            "1" => ["value" => 1, "label" => "Yes"]
        ];
    }
}
