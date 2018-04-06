<?php namespace Vulcan;

if ( ! defined( 'ABSPATH' ) ) exit;

date_default_timezone_set('America/New_York');

define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

require_once( 'autoloader.php' );

add_action(
	'init',
	function()
	{
		if (is_admin()) {
			$config = array(
				'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
				'proper_folder_name' => 'plugin-name', // this is the name of the folder your plugin lives in
				'api_url' => 'https://api.github.com/repos/TJSeabury/Vulcan', // the GitHub API url of your GitHub repo
				'raw_url' => 'https://raw.github.com/TJSeabury/Vulcan/master', // the GitHub raw url of your GitHub repo
				'github_url' => 'https://github.com/TJSeabury/Vulcan', // the GitHub url of your GitHub repo
				'zip_url' => 'https://github.com/TJSeabury/Vulcan/archive/master.zip', // the zip url of the GitHub repo
				'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
				'requires' => '4.0', // which version of WordPress does your plugin require?
				'tested' => '4.9.4', // which version of WordPress is your plugin tested up to?
				'readme' => 'README.md', // which file to use as the readme for the version number
				'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
			);
			new utils\WP_GitHub_Updater( $config );
		}
	}
);

$vulcan = new Vulcan(
	__DIR__,
	VULCANTHEMEROOT,
	time()
);

$vulcan->initAdmin();

$vulcan->initVCElements(
	array(
		'/vcElements/vulcan-filtered-post-category.php',
		//'/vcElements/vulcan-flexbox.php', ! currently broken !
		'/vcElements/vulcan-post-slider.php',
		'/vcElements/vulcan-hero-slider.php',
		'/vcElements/vulcan-events.php',
		'/vcElements/vulcan-facebook-page.php'
	)
);

$vulcan->initWidgets(
	array(
		'Vulcan_widget_upcoming_events'
	)
);

$vulcan->expose_mk_options();

$vulcan->initStyles(
	'/assests/css/modules/',
	'/assets/css/',
	'aggregate.min.css'
);

$vulcan->initScripts(
	array(
		'/assets/js/',
		'/vendor/js/',
		'/admin/js/'
	)
);

$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();











add_action( 'wp_enqueue_scripts', function()
{
	$coreJsPath = $this->themeUri . '/public/js/vulcancoreutilities.js';
	wp_register_script( 'vulcanCoreJS', $coreJsPath );
	wp_localize_script( 'vulcanCoreJS', 'wpMeta', array( 'siteURL' => get_option( 'siteurl' ) ) );
	wp_enqueue_script( 'vulcanCoreJS', $coreJsPath, array(), utils\FileTools::getVersion( $coreJsPath ), true);
}, 2 );

add_action( 'wp_enqueue_scripts', function()
{
	$mainJsPath = $this->themeUri . '/public/js/main.js';
	wp_enqueue_script( 'vulcanMainJS', $mainJsPath, array(), utils\FileTools::getVersion( $mainJsPath ), true);
}, 1 );

add_action( 'wp_enqueue_scripts', 'vulcan_theme_css', 42 );
function vulcan_theme_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/theme.css';
	wp_enqueue_style( 'vulcan-theme-css', $mainCSSPath, array(), utils\FileTools::getVersion($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_components_css', 32 );
function vulcan_components_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/assets/css/vulcan.css';
	wp_enqueue_style( 'vulcan-component-css', $mainCSSPath, array(), utils\FileTools::getVersion($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_core_js', 10 );
function vulcan_core_js() {
	$coreJsPath = get_stylesheet_directory_uri() . '/assets/js/utils.js';
	wp_register_script( 'vulcan-utils-js', $coreJsPath );
	wp_localize_script( 'vulcan-utils-js', 'wpMeta', array( 'siteURL' => get_option('siteurl') ) );
	wp_enqueue_script( 'vulcan-utils-js', $coreJsPath, array(), utils\FileTools::getVersion($coreJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_main_js', 9 );
function vulcan_main_js() {
	$mainJsPath = get_stylesheet_directory_uri() . '/assets/js/main.js';
	wp_enqueue_script( 'vulcan-main-js', $mainJsPath, array(), utils\FileTools::getVersion($mainJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_snap_js', 10 );
function vulcan_snap_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/snap.svg-min.js';
	wp_enqueue_script( 'snap-svg-js', $snapJsPath, array(), utils\FileTools::getVersion($snapJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_skrollr_js', 10 );
function vulcan_skrollr_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/skrollr.js';
	wp_enqueue_script( 'skrollr-js', $snapJsPath, array(), utils\FileTools::getVersion($snapJsPath), true);
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
			utils\FileTools::getVersion($path),
			true
		);
	},
	10
);