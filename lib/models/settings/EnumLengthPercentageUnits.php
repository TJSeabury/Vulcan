<?php

namespace Vulcan\lib\models\settings;

use \Vulcan\lib\types\AEnum as Enum;

/**
 * This is a work-around because we are still on php7.4 
 * and Enums are not available until php8.1. :c
 * This can probably be replaced with native when(if) we switch to php8.1.
 */
abstract class EnumLengthPercentageUnits extends Enum
{
    const CH = 'ch';
    const EM = 'em';
    const EX = 'ex';
    const REM = 'rem';
    const VH = 'vh';
    const VW = 'vw';
    const VMIN = 'vmin';
    const VMAX = 'vmax';
    const PX = 'px';
    const CM = 'cm';
    const MM = 'mm';
    const IN = 'in';
    const PC = 'pc';
    const PT = 'pt';
    const PERCENT = '%';
    const AUTO = 'auto';
}
