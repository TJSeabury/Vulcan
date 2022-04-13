<?php

namespace Vulcan\lib\models\settings\border;

class BorderGroup
{
    public BorderCollection $borders;

    public function __construct(
        array $settings,
        BorderCollection $borders
    ) {
        $this->settings = $settings;
        $this->borders = $borders;
    }
}
