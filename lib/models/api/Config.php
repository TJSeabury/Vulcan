<?php 

namespace Vulcan\lib\models\api;

class Config {
    public static $defaults = [
        'namespace' => 'vulcan',
        'version' => 'v1',
        'requireAuth' => true
    ];
    public static function hasSetting( string $key ) {
        if ( !empty( $key ) && 
        true === array_key_exists( $key, self::$defaults ) ) {
            return true;
        }
        return false;
    }
}