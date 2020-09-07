<?php
namespace Swissup\EasySlide\Model;

use Swissup\EasySlide\Api\Data\SliderInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Slider extends \Magento\Framework\Model\AbstractModel
    implements SliderInterface, IdentityInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'easyslide_slider';

    /**
     * @var string
     */
    protected $_cacheTag = 'easyslide_slider';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'easyslide_slider';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\EasySlide\Model\ResourceModel\Slider');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $sliderConfig = $this->getSliderConfig();
        if (is_array($sliderConfig)) {
            $this->addData($sliderConfig);
        }

        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        // Prepare slider config before save.
        $configKeys = [
            'theme', 'speed', 'pagination', 'navigation', 'scrollbar',
            'autoplay', 'effect', 'startRandomSlide', 'spaceBetween', 'lazy',
            'loadPrevNext', 'sizes'
        ];
        $sliderConfig = array_intersect_key(
            $this->getData(),
            array_flip($configKeys)
        );
        $this->setSliderConfig($sliderConfig);

        return parent::beforeSave();
    }

    public function loadByIdentifier($field, $value)
    {
        $this->load($value, $field);
        return $this;
    }

    public function getSlides()
    {
        return $this->_getResource()->getSlides($this->getId());
    }

    public function getOptionSliders()
    {
        $sliders = $this->_getResource()->getOptionSliders();
        $options = [];
        foreach ($sliders as $item) {
            $options[] = ['value' => $item['identifier'], 'label' => $item['title']];
        }

        return $options;
    }

    /**
     * Get slider_id
     *
     * return int
     */
    public function getSliderId()
    {
        return $this->getData(self::SLIDER_ID);
    }

    /**
     * Get identifier
     *
     * return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Get title
     *
     * return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get slider_config
     *
     * return string
     */
    public function getSliderConfig()
    {
        return $this->getData(self::SLIDER_CONFIG);
    }

    /**
     * Get is_active
     *
     * return int
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get sizes of image in slide
     *
     * @return array|null
     */
    public function getSizes()
    {
        return $this->getData(self::SIZES);
    }


    /**
     * Set slider_id
     *
     * @param int $sliderId
     * return \Swissup\Easyslide\Api\Data\SliderInterface
     */
    public function setSliderId($sliderId)
    {
        return $this->setData(self::SLIDER_ID, $sliderId);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * return \Swissup\Easyslide\Api\Data\SliderInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set title
     *
     * @param string $title
     * return \Swissup\Easyslide\Api\Data\SliderInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set slider_config
     *
     * @param string $sliderConfig
     * return \Swissup\Easyslide\Api\Data\SliderInterface
     */
    public function setSliderConfig($sliderConfig)
    {
        return $this->setData(self::SLIDER_CONFIG, $sliderConfig);
    }

    /**
     * Set is_active
     *
     * @param int $isActive
     * return \Swissup\Easyslide\Api\Data\SliderInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}
