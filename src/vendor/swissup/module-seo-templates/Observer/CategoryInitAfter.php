<?php

namespace Swissup\SeoTemplates\Observer;

class CategoryInitAfter extends AbstractInitAfter
{
    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $this->updateMetadata(
                $observer->getEvent()->getCategory(),
                \Swissup\SeoTemplates\Model\Template::ENTITY_TYPE_CATEGORY
            );
            $this->optimizeMetadata($observer->getEvent()->getCategory());
        }

        return $this;
    }
}
