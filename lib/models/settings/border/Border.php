<?php

namespace Vulcan\lib\models\settings\border;

use OzdemirBurak\Iris\BaseColor;

class Border
{
    public BaseColor $color;
    public float $radius;
    public string $style;
    public int $width;

    public function __construct()
    {
    }
}
