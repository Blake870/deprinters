<?php
namespace Swissup\EasySlide\Model\ResourceModel;

/**
 * Easyslide Slider mysql resource
 */
class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Swissup\EasySlide\Model\SlidesFactory
     */
    protected $slidesFactory;

    /**
     * {@inheritdoc}
     */
    protected $_serializableFields = ['slider_config' => [null, []]];

    /**
     * Constructor
     *
     * @param \Swissup\EasySlide\Model\SlidesFactory            $slidesFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $connectionName
     */
    public function __construct(
        \Swissup\EasySlide\Model\SlidesFactory $slidesFactory,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->slidesFactory = $slidesFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_easyslide_slider', 'slider_id');
    }

    public function getSlides($sliderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_easyslide_slides'))
        ->where('slider_id = ?', $sliderId)
        ->where('is_active = ?', 1)
        ->order('sort_order');

        return $connection->fetchAll($select);
    }

    public function getOptionSliders()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable())
        ->where('is_active = ?', 1);

        return $connection->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $slide = $this->slidesFactory->create();
        $slidesArray = $slide->getSlides($object->getId());
        foreach ($slidesArray as $slideItem) {
            // Delete each slide individually to remove imgae files.
            $slide->load($slideItem['slide_id'])->delete();
        }

        return parent::_beforeDelete($object);
    }
}
