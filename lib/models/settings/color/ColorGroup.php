<?php

namespace Vulcan\lib\models\settings\color;

use \Vulcan\lib\types\ColorCollection;
use \Vulcan\lib\types\DuotoneCollection;
use \Vulcan\lib\types\GradientCollection;

class ColorGroup
{
    public $link = false;
    public $custom = true;
    public $customDuotone = true;
    public $customGradient = true;

    public ColorCollection $palette;
    public DuotoneCollection $duotones;
    public GradientCollection $gradients;

    public function __construct(
        array $settings,
        ColorCollection $palette,
        DuotoneCollection $duotones,
        GradientCollection $gradients
    ) {
        $this->settings = $settings; // @todo validate against defaults
        $this->palette = $palette;
        $this->duotones = $duotones;
        $this->gradients = $gradients;
    }
}
