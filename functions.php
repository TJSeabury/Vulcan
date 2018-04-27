<?php namespace Vulcan;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'VULCANTHEMEROOT', get_stylesheet_directory_uri() );

require_once( 'autoloader.php' );

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once get_template_directory() . '/vendor/TGM-Plugin-Activation-2.6.1/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', '\Vulcan\_vulcan_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function _vulcan_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'               => 'Beaver Builder', // The plugin name.
			'slug'               => 'bb-plugin', // The plugin slug (typically the folder name).
			'source'             => get_template_directory() . '/vendor/bb-plugin.zip', // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),

		/* array(
			'name'      => 'Beaver Builder',
			'slug'      => 'beaver-builder-lite-version',
			'required'  => true,
		), */

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => '_vulcan',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', '_vulcan' ),
			'menu_title'                      => __( 'Install Plugins', '_vulcan' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', '_vulcan' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', '_vulcan' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', '_vulcan' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'_vulcan'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'_vulcan'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'_vulcan'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'_vulcan'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'_vulcan'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'_vulcan'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'_vulcan'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'_vulcan'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'_vulcan'
			),
			'return'                          => __( 'Return to Required Plugins Installer', '_vulcan' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', '_vulcan' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', '_vulcan' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', '_vulcan' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', '_vulcan' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', '_vulcan' ),
			'dismiss'                         => __( 'Dismiss this notice', '_vulcan' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', '_vulcan' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', '_vulcan' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa( $plugins, $config );
}

global $vulcan;
$vulcan = new Vulcan(
	__DIR__,
	VULCANTHEMEROOT,
	time()
);

/**
 * _vulcan functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package _vulcan
 */

if ( ! function_exists( '_vulcan_setup' ) )
{
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function _vulcan_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
		load_theme_textdomain( '_vulcan', get_template_directory() . '/languages' );

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
			'menu-1' => esc_html__( 'Primary', '_vulcan' ),
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
		add_theme_support( 'custom-background', apply_filters( '_vulcan_custom_background_args', array(
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
}
add_action( 'after_setup_theme', '\Vulcan\_vulcan_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function _vulcan_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( '_vulcan_content_width', 640 );
}
add_action( 'after_setup_theme', '\Vulcan\_vulcan_content_width', 0 );

/**
 * Register widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function _vulcan_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', '_vulcan' ),
		'id'            => 'sidebar',
		'description'   => esc_html__( 'Add widgets here.', '_vulcan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer', '_vulcan' ),
		'id'            => 'footer',
		'description'   => esc_html__( 'Add widgets here.', '_vulcan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', '\Vulcan\_vulcan_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function _vulcan_scripts() {
	wp_enqueue_style( ' _vulcan-style', get_stylesheet_uri() );

	wp_enqueue_script( ' _vulcan-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( ' _vulcan-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', '\Vulcan\_vulcan_scripts' );

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

$vulcan->setMaxImageResolutionAndQuality( 2000, 2000, 70 );

$vulcan->initAdmin();

/*$vulcan->initVCElements(
	array(
		'/vcElements/vulcan-filtered-post-category.php',
		//'/vcElements/vulcan-flexbox.php', ! currently broken !
		'/vcElements/vulcan-post-slider.php',
		'/vcElements/vulcan-hero-slider.php',
		'/vcElements/vulcan-events.php',
		'/vcElements/vulcan-facebook-page.php'
	)
);*/

add_action(
	'init',
	$vulcan->initBuilderModules()
);

/*$vulcan->initWidgets(
	array(
		'Vulcan_widget_upcoming_events'
	)
);*/

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



