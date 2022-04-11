<?php 

namespace Vulcan\lib\types;

class ColorStops {
    public function __construct( ColorStopCollection $colorStops ) {
        $this->stops = $colorStops;
    }

    protected function print(): string {
        $last = count( $this->$stops ) - 1;
        $i = 0;
        return array_reduce(
            $this->stops,
            function( $acum, $stop ) {
                return $acum . $stop->print() . $i++ != $last ? ", " : "";
            },
            ""
        );
    }
}