<?php namespace Vulcan;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

require_once( 'autoloader.php' );

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