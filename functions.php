<?php namespace Vulcan;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * _s functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package _s
 */

if ( ! function_exists( '_s_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function _s_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on _s, use a find and replace
		 * to change '_s' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( '_s', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', '_s' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( '_s_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', '\Vulcan\_s_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function _s_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( '_s_content_width', 640 );
}
add_action( 'after_setup_theme', '\Vulcan\_s_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function _s_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', '_s' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', '_s' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', '\Vulcan\_s_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function _s_scripts() {
	wp_enqueue_style( '_s-style', get_stylesheet_uri() );

	wp_enqueue_script( '_s-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( '_s-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', '\Vulcan\_s_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}


define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

require_once( 'autoloader.php' );

/*add_action(
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
);*/

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

//

/*$vulcan->initStyles(
	'/assests/css/modules/',
	'/assets/css/',
	'aggregate.min.css'
);*/

/*$vulcan->initScripts(
	array(
		'/assets/js/',
		'/vendor/js/',
		'/admin/js/'
	)
);*/

$vulcan->initFilters();

$vulcan->enableModulesBasedOnThemeOptions();












/*add_action( 'wp_enqueue_scripts', 'vulcan_theme_css', 42 );
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
*/



