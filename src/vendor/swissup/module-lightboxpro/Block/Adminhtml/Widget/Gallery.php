<?php
namespace Swissup\Lightboxpro\Block\Adminhtml\Widget;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * Gallery field name suffix
     *
     * @var string
     */
    protected $fieldNameSuffix = '';

    /**
     * Gallery html id
     *
     * @var string
     */
    protected $htmlId = 'gallery';

    /**
     * Gallery name
     *
     * @var string
     */
    protected $name = 'gallery';

    /**
     * @var string
     */
    protected $formName = 'widget_options_form';

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\Form $form
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form $form,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $storeManager, $registry, $form, $data);
    }

    /**
     * Get gallery images
     *
     * @return array|null
     */
    public function getImages()
    {
        $result = [];
        $widgetParameters = [];
        $widget = $this->registry->registry('current_widget_instance');

        if ($widget) {
            $widgetParameters = $widget->getWidgetParameters();
        } elseif ($widgetOptions = $this->getLayout()->getBlock('wysiwyg_widget.options')) {
            $widgetParameters = $widgetOptions->getWidgetValues();
        }

        if (array_key_exists('gallery', $widgetParameters)) {
            $imagesArr = explode(';', $widgetParameters['gallery']);
            if (count($imagesArr) > 0) {
                $result['images'] = [];
                foreach ($imagesArr as $image) {
                    parse_str($image, $res);
                    $result['images'][] = $res;
                }
            }
        }

        return $result;
    }

    public function prepareElementHtml($element)
    {
        $this->addChild(
            'content',
            'Swissup\Lightboxpro\Block\Adminhtml\Widget\Gallery\Content'
        );

        $hiddenHtml = '';
        $hidden = $this->elementFactory->create('hidden', ['data' => $element->getData()]);
        $hidden->setId("gallery-value")->setForm($element->getForm());
        if ($element->getRequired()) {
            $hidden->addClass('required-entry');
        }
        $hiddenHtml = $hidden->getElementHtml();
        $element->setValue('');

        $element->setData('after_element_html', $hiddenHtml . $this->toHtml());

        return $element;
    }
}
