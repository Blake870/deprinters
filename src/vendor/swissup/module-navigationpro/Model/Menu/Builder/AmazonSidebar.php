<?php

namespace Swissup\Navigationpro\Model\Menu\Builder;

class AmazonSidebar extends \Swissup\Navigationpro\Model\Menu\Builder
{
    protected function prepareSettings()
    {
        $this->setSettings([
            'max_depth' => 0,
            'identifier' => 'amazon_sidebar',
        ]);
    }

    protected function prepareItems()
    {
        $this->setItems([
            'categories' => [
                'method' => 'importCategories'
            ],
        ]);

        return $this;
    }

    /**
     * Create widget for newly created menu
     */
    protected function afterSave()
    {
        $menu = $this->getMenu();
        $widget = $this->widgetFactory->create();
        $widget
            ->setType('Swissup\Navigationpro\Block\Widget\Menu')
            ->setCode('navigationpro_menu')
            ->setThemeId($this->getThemeId())
            ->setTitle('NavigationPro ' . $menu->getIdentifier())
            ->setStoreIds($this->getStoreIds())
            ->setSortOrder(0)
            ->setPageGroups([
                [
                    'page_group' => 'pages',
                    'pages' => [
                        'page_id' => '0',
                        'for' => 'all',
                        'layout_handle' => 'catalog_category_view',
                        'block' => 'sidebar.main.top',
                    ],
                    'page_layouts' => [
                        'layout_handle' => '',
                    ],
                ],
            ])
            ->setWidgetParameters([
                'identifier'         => $menu->getIdentifier(),
                'show_active_branch' => 1,
                'theme'              => 'compact',
                'orientation'        => 'vertical',
                'wrap'               => 1,
            ]);

        try {
            $widget->save();
        } catch (\Exception $e) {
        }
    }
}
