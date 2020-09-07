<?php

namespace Swissup\Easybanner\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

class BannerContent extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'banner_content';

    /**
     * @var \Swissup\Easybanner\Model\Data\Image
     */
    private $imageInfo;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Swissup\Easybanner\Model\Data\Image $imageInfo
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Swissup\Easybanner\Model\Data\Image $imageInfo,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->imageInfo = $imageInfo;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $baseUrl = $this->imageInfo->getBaseUrl();

            foreach ($dataSource['data']['items'] as & $item) {
                $productImgUrl = $baseUrl . '/' . ltrim($item['image'], '/');

                $item[$fieldName . '_src'] = $productImgUrl;
                $item[$fieldName . '_alt'] = $item['title'] ?: $item['image'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'easybanner/banner/edit',
                    ['banner_id' => $item['banner_id']]
                );
                $item[$fieldName . '_orig_src'] = $productImgUrl;
            }
        }

        return $dataSource;
    }
}
