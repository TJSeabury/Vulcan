<?php 

namespace Vulcan\lib\models\settings\color;

class Duotone {
    public function __constructor(
        string $name,
        string $colorOne,
        string $colorTwo,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title( $name );
        $this->colors = [
            $colorOne,
            $colorTwo,
        ];
    }
}