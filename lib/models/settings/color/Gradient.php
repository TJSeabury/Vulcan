<?php 

namespace Vulcan\lib\models\settings\color;

use OzdemirBurak\Iris\BaseColor;

final class Gradient {

    private $type;
    private $angle;
    private $colorStops;

    public function __constructor(
        string $name,
        string $type,
        Angle $angle,
        ColorStops $colorStops,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title( $name );
        if ( !EnumGradientTypes::isValidValue( $type ) ) throw new Error("Invalid gradient type!");
        $this->type = $type;
        $this->angle = $angle;
        $this->colorStops = $colorStops;
        $this->gradient = $this->print();
    }

    public function print(): string {
        return "{$type}( {$angle->degrees()}deg, {$colorStops->print()} )";
    }
}