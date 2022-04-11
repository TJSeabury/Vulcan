<?php 

namespace Vulcan\lib\utils;

class Math {
    public static function degreesToRadians( float $degrees ): float {
        return $degrees * ( M_PI / 180 );
    }
    public static function radiansToDegrees( float $radians ): float {
        return $radians * ( 180 / M_PI );
    }
}