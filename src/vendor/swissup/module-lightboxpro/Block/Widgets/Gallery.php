<?php
namespace Swissup\Lightboxpro\Block\Widgets;

/**
 * Class lightboxpro gallery widget
 */
class Gallery extends \Magento\Framework\View\Element\Template
     implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for widget
     */
    const DEFAULT_TEMPLATE = 'widget/gallery.phtml';

    /**
     * @var \Swissup\Lightboxpro\Helper\Image $imageHelper
     */
    protected $imageHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Lightboxpro\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Lightboxpro\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_TEMPLATE);
        }

        return parent::_construct();
    }

    public function getGalleryImagesJson()
    {
        $counter = 0;
        $imagesItems = [];
        $imgWidth = (int)$this->getImgWidth();
        $imgHeight = (int)$this->getImgHeight();
        $thumbWidth = (int)$this->getThumbWidth();
        $thumbHeight = (int)$this->getThumbHeight();
        $imagesArr = explode(';', htmlspecialchars_decode($this->getData('gallery')));
        $baseUrl = $this->imageHelper->getBaseUrl();

        foreach ($imagesArr as $image) {
            parse_str($image, $res);

            $thumbUrl = $this->imageHelper->resize($res['file'], $thumbWidth, $thumbHeight);
            $imgUrl = $this->imageHelper->resize($res['file'], $imgWidth, $imgHeight);
            $fullUrl = $baseUrl . '/' . $res['file'];

            $imagesItems[] = [
                'thumb' => $thumbUrl,
                'img' => $imgUrl,
                'full' => $fullUrl,
                'caption' => $res['label'],
                'position' => $res['position'],
                'isMain' => $counter == 0 ? 'true' : 'false'
            ];

            $counter++;
        }

        return json_encode($imagesItems);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
