<?php

namespace Swissup\EasySlide\Block;

use Magento\Framework\View\Element\Template;

class Image extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'image.phtml';

    /**
     * @var \Swissup\EasySlide\Helper\Image
     */
    protected $helper;


    /**
     * @param \Swissup\EasySlide\Helper\Image $helper
     * @param Template\Context                $context
     * @param array                           $data
     */
    public function __construct(
        \Swissup\EasySlide\Helper\Image $helper,
        Template\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Render image html
     *
     * @param  array  $slide
     * @param  bool   $isLazy
     * @return string
     */
    public function render(array $slide, $sizes, $isLazy)
    {
        $this->assign('isLazy', $isLazy);
        $this->assign('slide', $slide);
        $this->assign(
            'attributes',
            $this->buildAttributes($slide['image'], $sizes)
        );
        return parent::toHtml();
    }

    /**
     * Resize image using easyslide helper
     *
     * @param  string   $imageFile
     * @param  int      $w
     * @param  null|int $h
     * @return string|boolean
     */
    public function resize($imageFile, $w, $h = null)
    {
        return $this->helper->resize($imageFile, $w, $h);
    }

    /**
     * Build `srcset` and `sizes` attributes for image
     *
     * @param  string $imageFile
     * @param  array  $sizes
     * @return array
     */
    public function buildAttributes($imageFile, $sizes)
    {
        $originalWidth = $this->helper->getImageWidth($imageFile);
        if (is_array($sizes) && !empty($sizes) && $originalWidth) {
            $srcsetAttr = [];
            $sizesAttr = [];
            foreach ($sizes as $item) {
                $width = str_replace('px', '', $item['image_width']); // Remove 'px' just in case...
                $query = $item['media_query'];
                if (!$width || $width >= $originalWidth) {
                    continue;
                }

                $srcsetAttr[] = "{$this->resize($imageFile, $width)} {$width}w";
                $sizesAttr[] = "{$query} {$width}px";

            }

            $srcsetAttr[] = "{$this->getImageUrl($imageFile)} {$originalWidth}w";
            $sizesAttr[] = "{$originalWidth}px";

            return [
                'srcset' => implode(', ', $srcsetAttr),
                'sizes' => implode(', ', $sizesAttr)
            ];
        }

        return [];
    }

    /**
     * Get image URL
     *
     * @param  string $imageFile
     * @return string
     */
    public function getImageUrl($imageFile)
    {
        return $this->helper->getBaseUrl() . $imageFile;
    }
}
