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
	'/assests/css/modules/',
	'/assets/css/',
	'aggregate.min.css'
);

/*
* This needs to be rewritten to accept filenames and folders.
*/
$vulcan->initScripts(
	array(
		'/assets/js/',
		'/vendor/js/',
		'/admin/js/'
	)
);

add_action( 'wp_enqueue_scripts', function()
{
	$coreJsPath = $this->themeUri . '/public/js/vulcancoreutilities.js';
	wp_register_script( 'vulcanCoreJS', $coreJsPath );
	wp_localize_script( 'vulcanCoreJS', 'wpMeta', array( 'siteURL' => get_option( 'siteurl' ) ) );
	wp_enqueue_script( 'vulcanCoreJS', $coreJsPath, array(), utils\FileVersion::getVersion( $coreJsPath ), true);
}, 2 );

add_action( 'wp_enqueue_scripts', function()
{
	$mainJsPath = $this->themeUri . '/public/js/main.js';
	wp_enqueue_script( 'vulcanMainJS', $mainJsPath, array(), utils\FileVersion::getVersion( $mainJsPath ), true);
}, 1 );

add_action( 'wp_enqueue_scripts', 'vulcan_theme_css', 42 );
function vulcan_theme_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/theme.css';
	wp_enqueue_style( 'vulcan-theme-css', $mainCSSPath, array(), utils\FileVersion::getVersion($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_components_css', 32 );
function vulcan_components_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/assets/css/vulcan.css';
	wp_enqueue_style( 'vulcan-component-css', $mainCSSPath, array(), utils\FileVersion::getVersion($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_core_js', 10 );
function vulcan_core_js() {
	$coreJsPath = get_stylesheet_directory_uri() . '/assets/js/utils.js';
	wp_register_script( 'vulcan-utils-js', $coreJsPath );
	wp_localize_script( 'vulcan-utils-js', 'wpMeta', array( 'siteURL' => get_option('siteurl') ) );
	wp_enqueue_script( 'vulcan-utils-js', $coreJsPath, array(), utils\FileVersion::getVersion($coreJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_main_js', 9 );
function vulcan_main_js() {
	$mainJsPath = get_stylesheet_directory_uri() . '/assets/js/main.js';
	wp_enqueue_script( 'vulcan-main-js', $mainJsPath, array(), utils\FileVersion::getVersion($mainJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_snap_js', 10 );
function vulcan_snap_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/snap.svg-min.js';
	wp_enqueue_script( 'snap-svg-js', $snapJsPath, array(), utils\FileVersion::getVersion($snapJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_skrollr_js', 10 );
function vulcan_skrollr_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/skrollr.js';
	wp_enqueue_script( 'skrollr-js', $snapJsPath, array(), utils\FileVersion::getVersion($snapJsPath), true);
}

add_action(
	'wp_enqueue_scripts',
	function()
	{
		$path = get_stylesheet_directory_uri() . '/vendor/js/skrollr.js';
		wp_enqueue_script(
			'skrollr-js',
			$path,
			array(),
			utils\FileVersion::getVersion($path),
			true
		);
	},
	10
);





$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();