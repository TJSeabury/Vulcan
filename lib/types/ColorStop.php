<?php 

namespace Vulcan\lib\types;

use \OzdemirBurak\Iris\Color\Factory as MakeColor;

class ColorStop {
    public function __construct( string $color, float $stop ) {
        $this->color = MakeColor( $color );
        $this->stop = new Percent( $stop );
    }

    public function print(): string {
        return "{$this->color->toRgb()->__toString()} {$this->stop->print()}";
    }
}