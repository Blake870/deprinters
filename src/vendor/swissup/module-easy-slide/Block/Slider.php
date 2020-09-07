<?php

namespace Swissup\EasySlide\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Slider extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'slider.phtml';

    /**
     * @var \Swissup\EasySlide\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Swissup\EasySlide\Model\Slider
     */
    protected $slider = null;

    /**
     * @var Image
     */
    protected $imageRenderer;

    /**
     * @param Template\Context                           $context
     * @param \Swissup\EasySlide\Model\SliderFactory     $sliderFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\Json\EncoderInterface   $jsonEncoder
     * @param Image                                      $jsonEncoder
     * @param array                                      $imageRenderer
     */
    public function __construct(
        Template\Context $context,
        \Swissup\EasySlide\Model\SliderFactory $sliderFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Image $imageRenderer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sliderFactory = $sliderFactory;
        $this->filterProvider = $filterProvider;
        $this->imageRenderer = $imageRenderer;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Get slider model
     *
     * @return \Swissup\EasySlide\Model\Slider|boolean
     */
    public function getSlider()
    {
        $identifier = $this->getIdentifier();
        if (!$identifier) {
            return false;
        }

        if (!$this->slider) {
            $this->slider = $this->sliderFactory->create()
                ->loadByIdentifier('identifier', $identifier);
        }

        return $this->slider->getIsActive() ? $this->slider : false;
    }

    /**
     * Get processed slide description
     *
     * @param  string $description
     * @return string
     */
    public function getSlideDescription($description)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $result = $this->filterProvider->getBlockFilter()
            ->setStoreId($storeId)
            ->filter($description);

        return $result;
    }

    /**
     * Get JSON slider config for mage-init
     *
     * @return string
     */
    public function getSliderConfig()
    {
        if (!$slider = $this->getSlider()) {
            return '{}';
        }

        $config = [
            'effect' => $slider->getData('effect'),
            'speed' => (int)$slider->getData('speed'),
            'autoplay' => false,
            'startRandomSlide' => (bool)$slider->getData('startRandomSlide'),
            'spaceBetween' => (int)$slider->getData('spaceBetween')
        ];

        if ($slider->getData('autoplay')) {
             $config['autoplay'] = [
                'delay' => $slider->getData('autoplay')
             ];
        }

        if ($slider->getData('pagination')) {
            $config['pagination'] = [
                'el' => '.swiper-pagination',
                'clickable' => true,
                'type' => 'bullets'
            ];
        }

        if ($slider->getData('navigation')) {
            $config['navigation'] = [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev'
            ];
        }

        if ($slider->getData('scrollbar')) {
            $config['scrollbar'] = [
                'el' => '.swiper-scrollbar'
            ];
        }

        // lazy loading
        if ($slider->getData('lazy')) {
            $config['preloadImages'] = false;
            $config['autoHeight'] = false;
            $config['lazy'] = [
                'loadPrevNext' => !!$slider->getData('loadPrevNext')
            ];
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Get slide image renderer
     *
     * @return Image
     */
    public function getImageRenderer()
    {
        return $this->imageRenderer;
    }
}
