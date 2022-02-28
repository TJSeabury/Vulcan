<?php namespace Vulcan;

/**
 * Disallow direct access with prejudice.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * VULCANTHEMEROOT is the base url of the theme.
 */
define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

/**
 * Initializes PSR-4 autoloading handled by composer.
 */
require_once( 'vendor/autoload.php' );

/**
 * Ok, now let's setup the theme.
 */
global $vulcan;
$vulcan = new Vulcan(
	__DIR__,
	VULCANTHEMEROOT,
	time()
);

$vulcan->forcePlugins();

/**
 * @todo fix this
 */
//$vulcan->checkUpdates();

$vulcan->setup();

$vulcan->setMaxImageResolutionAndQuality( 2000, 2000, 70 );

$vulcan->initAdmin();

/**
 * @todo fix this
 */
/* $vulcan->initWidgets(
	array(
		'Vulcan_widget_upcoming_events'
	)
); */

$vulcan->initStyles(
	'/assets/css/modules/',
	'/assets/css/',
	'bundle.min.css'
);

$vulcan->initScripts(
	[
		'/assets/js/',
		'/vendor/js/',
		'/admin/js/'
    ]
);

$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();

