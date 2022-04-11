<?php 

namespace Vulcan\lib\models\settings\color;

use \Vulcan\lib\utils\AEnum as Enum;

abstract class EnumGradientTypes extends Enum {
    const Linear = "linear-gradient";
    const Radial = "radial-gradient";
}