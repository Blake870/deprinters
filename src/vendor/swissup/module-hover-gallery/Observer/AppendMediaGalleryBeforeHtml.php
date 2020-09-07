<?php
namespace Swissup\HoverGallery\Observer;

use Magento\Framework\Data\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Swissup\HoverGallery\Helper\Data as DataHelper;

class AppendMediaGalleryBeforeHtml implements ObserverInterface
{
    /**
     * Constructor
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }
    /**
     * Append media gallery before rendering html
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Swissup\HoverGallery\Observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->dataHelper->isEnabled()) {
            return $this;
        }

        $productCollection = $observer->getEvent()->getCollection();

        if ($productCollection instanceof Collection) {
            $productCollection->load();

            foreach ($productCollection as $product) {
                $this->dataHelper->addGallery($product);
                $images = $this->getActiveMediaGalleryEntries($product);

                if ($images
                    && isset($images[1])
                    && $images[1]->getFile() != $product->getImage()
                ) {
                    $product->setHoverImage($images[1]);
                }
            }
        }

        return $this;
    }

    /**
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getActiveMediaGalleryEntries($product)
    {
        $images = $product->getMediaGalleryEntries();
        $result = [];

        foreach ($images as $image) {
            if ($image->isDisabled()) {
                continue;
            }
            $result[] = $image;
        }

        return $result;
    }
}
