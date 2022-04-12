<?php 

namespace Vulcan\lib\models\settings\color;

use OzdemirBurak\Iris\BaseColor;

final class Duotone {
    public function __constructor(
        string $name,
        BaseColor $colorOne,
        BaseColor $colorTwo,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title( $name );
        $this->colors = [
            $colorOne->toRgb()->__toString(),
            $colorTwo->toRgb()->__toString(),
        ];
    }
}