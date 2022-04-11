<?php 

namespace Vulcan\lib\models\settings\color;

class Gradient {
    public function __constructor(
        string $name,
        string $type,
        Angle $angle,
        array $colorStops,
        string $slug = null
    ) {
        $this->name = $name;
        $this->slug = !empty($slug) ? $slug : \sanitize_title( $name );
        if ( !EnumGradienTypes::isValidValue( $type ) ) throw new Error("Invalid gradient type!");
        $this->type = $type;
        $this->angle = $angle;
        $this->colorStops = $colorStops;
    }

    public function print(): string {
        return "{$type}( {$angle->degrees()}deg, {$colorStops->print()} )";
    }
}