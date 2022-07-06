<?php 

namespace Vulcan;

/*
* Lazy load classes for brevity.
* @param string $class The fully-qualified class name.
* @return void
*/
spl_autoload_register( function ( string $class )
{
    $prefix = 'Vulcan\\';
    $base_dir = __DIR__ . '/';
    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 )
	{
        return;
    }
    $relative_class = substr( $class, $len );
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
    if ( file_exists( $file ) )
	{
        include_once $file;
    }
} );