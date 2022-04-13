<?php

namespace Vulcan\lib\models\settings\color;

use \Vulcan\lib\types\AEnum as Enum;

/**
 * This is a work-around because we are still on php7.4 
 * and Enums are not available until php8.1. :c
 * This can probably be replaced with native when(if) we switch to php8.1.
 */
abstract class EnumGradientTypes extends Enum
{
    const Linear = "linear-gradient";
    const Radial = "radial-gradient";
}
