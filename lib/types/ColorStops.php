<?php

namespace Vulcan\lib\types;

class ColorStops extends ColorStopCollection
{
    public function __construct(array $colorStops)
    {
        parent::__construct($colorStops);
    }

    public function print(): string
    {
        $last = count($this->stops) - 1;
        $i = 0;
        return $this->reduce(
            $this->stops,
            function ($acum, $stop) use ($last, $i) {
                return $acum . $stop->print() . $i++ != $last ? ", " : "";
            },
            ""
        );
    }
}
