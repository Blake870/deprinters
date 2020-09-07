<?php

namespace Swissup\Navigationpro\Model\Menu\Builder;

class Stacked extends Simple
{
    protected function prepareSettings()
    {
        parent::prepareSettings();

        $this->setSettings([
            'max_depth' => 0,
            'identifier' => 'stacked',
            'css_class' => 'navpro-stacked navpro-effect-none',
        ]);
    }
}
