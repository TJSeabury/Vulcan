<?php 

namespace Vulcan\lib\models\settings\color;

class Color {
    public function __constructor(
        string $name,
        string $color,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title( $name );
        $this->color = $color;
    }
}