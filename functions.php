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
//$vulcan->initScripts();

$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();

/* ------------------------------------------- */


/* 
* This needs to be moved to it's own method within $vulcan and made more robust.
*/
add_action(
	'init',
	function()
	{
		$path = __DIR__ . '/assets/css/mk-options.css';
		$timestamp;
		if ( file_exists( $path ) ) {
			try {
				$timestamp = filemtime( $path );
			}
			finally
			{
				$timestamp = 0;
			}
		}
		if ( (int)get_option('global_assets_timestamp') > $timestamp )
		{
			$mko = get_option('Jupiter_options');
			ob_start();
			?>
/* 
* This file is dynamically generated.
* Last updated on: <?php echo date(DATE_RFC2822) . "\n"; ?>
*/
:root {
	--mk-skin-color: <?php echo $mko['skin_color']; ?>;
	--mk-link-color: <?php echo $mko['a_color']; ?>;
	--mk-link-hover-color: <?php echo $mko['a_color_hover']; ?>;
	--mk-strong-color: <?php echo $mko['strong_color']; ?>;
	--mk-grid-width: <?php echo $mko['grid_width']; ?>px;
	--mk-header-height: <?php echo $mko['header_height']; ?>px;
	--mk-responsive-header-height: <?php echo $mko['res_header_height']; ?>px;
	--mk-sticky-header-height: <?php echo $mko['header_scroll_height']; ?>px;
	--mk-logo: url("<?php echo $mko['logo']; ?>");
	--mk-font-all: <?php echo $mko['fonts'][0]['fontFamily']; ?>;
}
			<?php
			$css = ob_get_clean();
			$f = fopen( $path, 'wb' );
			fwrite( $f, $css );
			fclose( $f );
		}
		add_action( 'wp_enqueue_scripts',
			function()
			{
				$mainCSSPath = get_stylesheet_directory_uri() . '/assets/css/mk-options.css';
				wp_enqueue_style(
					'DIFDesignThemeOptions',
					$mainCSSPath,
					array(),
					vulcan_get_file_version( $mainCSSPath )
				);
			},
			32
		);
	}
);



/* 
* These need to be handled by $vulcan->initScripts()
*/
add_action( 'wp_enqueue_scripts', 'vulcan_theme_css', 42 );
function vulcan_theme_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/theme.css';
	wp_enqueue_style( 'vulcan-theme-css', $mainCSSPath, array(), vulcan_get_file_version($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_components_css', 32 );
function vulcan_components_css() {
	$mainCSSPath = get_stylesheet_directory_uri() . '/assets/css/vulcan.css';
	wp_enqueue_style( 'vulcan-component-css', $mainCSSPath, array(), vulcan_get_file_version($mainCSSPath) );
}

add_action( 'wp_enqueue_scripts', 'vulcan_core_js', 10 );
function vulcan_core_js() {
	$coreJsPath = get_stylesheet_directory_uri() . '/assets/js/utils.js';
	wp_register_script( 'vulcan-utils-js', $coreJsPath );
	wp_localize_script( 'vulcan-utils-js', 'wpMeta', array( 'siteURL' => get_option('siteurl') ) );
	wp_enqueue_script( 'vulcan-utils-js', $coreJsPath, array(), vulcan_get_file_version($coreJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_main_js', 9 );
function vulcan_main_js() {
	$mainJsPath = get_stylesheet_directory_uri() . '/assets/js/main.js';
	wp_enqueue_script( 'vulcan-main-js', $mainJsPath, array(), vulcan_get_file_version($mainJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_snap_js', 10 );
function vulcan_snap_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/snap.svg-min.js';
	wp_enqueue_script( 'snap-svg-js', $snapJsPath, array(), vulcan_get_file_version($snapJsPath), true);
}

add_action( 'wp_enqueue_scripts', 'vulcan_skrollr_js', 10 );
function vulcan_skrollr_js() {
	$snapJsPath = get_stylesheet_directory_uri() . '/vendor/js/skrollr.js';
	wp_enqueue_script( 'skrollr-js', $snapJsPath, array(), vulcan_get_file_version($snapJsPath), true);
}


/* 
* This can be removed after checking for any remaining dependencies.
*/
function vulcan_get_file_version( $url ) {
	$content_url = content_url();
	$filepath    = str_replace( $content_url, WP_CONTENT_DIR, $url );
	$filepath    = explode( '?', $filepath );
	$filepath    = array_shift( $filepath );
	// Ensure the file actually exists.
	if ( ! file_exists( $filepath ) ) {
		return;
	}
	// Attempt to read the file timestamp.
	try {
		$timestamp = filemtime( $filepath );
	} catch ( \Exception $e ) {
		return;
	}
	return $timestamp ? (string) $timestamp : null;
}