<?php 

namespace Vulcan\lib\types;

use \Vulcan\lib\utils\Math;

class Angle {
    public function __construct( float $degree ) {
        $this->sign = $degree >= 0 ? 1 : -1;
        $this->value = $degree % 360;
    }

    public function degrees(): float {
        return $this->value;
    }

    public function radians(): float {
        return Math::degreesToRadians( $this->value );
    }
}