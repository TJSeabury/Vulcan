<?php

namespace Vulcan\lib\models\settings\color;

use OzdemirBurak\Iris\BaseColor;

final class Color
{

    public string $name;
    public BaseColor $color;
    public string $slug;

    public function __constructor(
        string $name,
        BaseColor $color,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title($name);
        $this->color = $color;
    }
}
