<?php namespace Vulcan;

if ( ! defined( 'ABSPATH' ) ) exit;

date_default_timezone_set('America/New_York');

define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

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

/*
* Instantiate and setup child theme.
*/
$vulcan = new Vulcan(
	__DIR__,
	VULCANTHEMEROOT,
	time()
);

$vulcan->initAdmin();

$vulcan->initVCElements(
	array(
		'/vc-elements/vulcan-filtered-post-category.php',
		//'/vc-elements/vulcan-flexbox.php', ! currently broken !
		'/vc-elements/vulcan-post-slider.php',
		'/vc-elements/vulcan-hero-slider.php',
		'/vc-elements/vulcan-events.php',
		'/vc-elements/vulcan-facebook-page.php'
	)
);

$vulcan->initWidgets(
	array(
		'Vulcan_widget_upcoming_events'
	)
);

$vulcan->initStyles(
	'/public/css/modules/',
	'/public/css/',
	'aggregate.min.css'
);

/*
* This needs to be rewritten to accept filenames and folders.
*/
$vulcan->initScripts();

$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();