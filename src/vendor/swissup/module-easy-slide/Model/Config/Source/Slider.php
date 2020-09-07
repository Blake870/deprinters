<?php
/**
 * Slider source
 */
namespace Swissup\EasySlide\Model\Config\Source;

class Slider implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Swissup\EasySlide\Locale\ListsInterface
     */
    protected $_slider;

    /**
     * @param \Swissup\EasySlide\Api\Data\SliderInterface $slider
     */
    public function __construct(\Swissup\EasySlide\Api\Data\SliderInterface $slider)
    {
        $this->_slider = $slider;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_slider->getOptionSliders();
    }
}
