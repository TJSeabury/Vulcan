<?php 

namespace Vulcan\lib\types;

class Percent {
    public function __construct( float $v ) {
        if ( $v < 0 || $v > 100 ) throw new Error("Provided value {$v} is outside the allowed range of 0 <= n <= 100 !!");
        $this->value = $v;
    }

    public function print(): string {
        return "{$this->value}%";
    }
}