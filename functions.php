<?php

if ( ! defined( 'ABSPATH' ) ) exit;

date_default_timezone_set('America/New_York');

add_action(
	'init',
	function()
	{
		$path = __DIR__ . '/assets/css/mk-options.css';
	$timestamp;
		if ( file_exists( $path ) ) {
			try {
				$timestamp = filemtime( $path );
			} catch ( \Exception $e ) {
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

add_action( 'vc_before_init', 'vc_before_init_actions' );
function vc_before_init_actions()
{
	require_once( __DIR__ . '/vc-elements/vulcan-filtered-post-category.php' );
	//require_once( __DIR__ . '/vc-elements/vulcan-flexbox.php' );
	require_once( __DIR__ . '/vc-elements/vulcan-post-slider.php' );
	require_once( __DIR__ . '/vc-elements/vulcan-hero-slider.php' );
	require_once( __DIR__ . '/vc-elements/vulcan-events.php' );
	require_once( __DIR__ . '/vc-elements/vulcan-facebook-page.php' );
}

/*
* Include widgets
*/
add_action( 'widgets_init', 'register_vulcan_widgets' );
function register_vulcan_widgets() {
	require_once( __DIR__ . '/vulcan-widgets/vulcan-upcoming-events.php' );
    register_widget( 'Vulcan_widget_upcoming_event' );
}

add_action( 'init', 'add_taxonomies_to_pages' );
function add_taxonomies_to_pages()
{
	register_taxonomy_for_object_type( 'post_tag', 'page' );
	register_taxonomy_for_object_type( 'category', 'page' );
}

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

/*
* Adds the option to hide Gravity form field labels.
*/
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

/*
* Enables ajax for getting shortcodes dynamically
*/
add_action('wp_ajax_do_shortcode', 'ajax_do_shortcode');
add_action('wp_ajax_nopriv_do_shortcode', 'ajax_do_shortcode');

function ajax_do_shortcode()
{
	$output;
    switch( $_REQUEST['fn'] )
	{
		case 'do_shortcode':
			$output = do_shortcode( wp_unslash( $_REQUEST['shortcode'] ) );
			break;
		default:
			$output = 'Invalid shortcode';
			break;
	}
	$output = json_encode( $output );
	echo $output;
	wp_die();
}
















