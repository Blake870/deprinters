<?php

namespace Swissup\Navigationpro\Model\Menu\Builder;

class Simple extends \Swissup\Navigationpro\Model\Menu\Builder
{
    protected function prepareSettings()
    {
        $this->setSettings([
            'max_depth' => 0,
            'identifier' => 'simple',
        ]);
    }

    protected function prepareItems()
    {
        $this->setItems([
            'home' => [
                'name' => 'Home',
                'url_path' => '',
                'css_class' => 'navpro-home',
            ],
            'categories' => [
                'method' => 'importCategories'
            ],
            'contacts' => [
                'name' => 'Contacts',
                'url_path' => 'contact',
                'css_class' => 'navpro-contacts',
            ],
        ]);

        return $this;
    }
}
