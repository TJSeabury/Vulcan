<?php 

namespace Vulcan\lib\types;

/**
 * @see: https://stackoverflow.com/questions/1818765/extend-abstract-singleton-class
 */
abstract class ASingleton {
    private static $_instances = array();

    public static function getInstance() {
        $class = get_called_class();
        if ( !isset( self::$_instances[$class] ) ) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }

    abstract protected function __construct();
}