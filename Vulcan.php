<?php namespace Vulcan;

class Vulcan
{
	private $themePath = '';
	private $themeUri = '';
	private $initTime = 0;
	
	public function __construct( string $themeRootPath, string $themeRootUri, $Time )
	{
		$this->themePath = $themeRootPath;
		$this->themeUri = $themeRootUri;
		$this->initTime = $Time;
	}
	
	/*
	* Initializes Wordpress admin theme menu.
	*/
	public function initAdmin()
	{
		add_action( 'admin_init', function()
	    {
			add_settings_section( 
				'primary_settings',
				'Primary Settings',
				function( $arg )
			    {
					?>
					<p>Super important settings.</p>
					<?php
				},
				'vulcan'
			);
			
			/*
			* Theme Mode
			*/
			register_setting( 
				'vulcan_options', 
				'vulcan_theme_mode' 
			);
			add_settings_field(
				'vulcan_theme_mode',
				'Theme Mode',
				function( $args )
				{
				?>
					<label for="<?php echo $args['id']; ?>">
						<input type="checkbox" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" value="1" <?php checked( '1', get_option('vulcan_theme_mode') ); ?> />
						Live
					</labe>
				<?php
				},
				'vulcan',
				'primary_settings',
				array(
					'id' => 'vulcan_theme_mode'
				)
			);
			
			/*
			* Primary Color
			*/
			register_setting(
				'vulcan_options',
				'vulcan_primary_color'
			);
			add_settings_field(
				'vulcan_primary_color',
				'Primary Color',
				function( $args )
				{
				?>
					<div style="display:inline-block;width:25px;height:25px;margin:2px 0 0 0;background-color:<?php echo get_option('vulcan_primary_color'); ?>;vertical-align:top;"></div>
					<input type="text" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" value="<?php echo get_option('vulcan_primary_color'); ?>" />
				<?php
				},
				'vulcan',
				'primary_settings',
				array(
					'id' => 'vulcan_primary_color'
				)
			);
			
			/*
			* Minify css
			*/
			register_setting(
				'vulcan_options',
				'vulcan_minify_css'
			);
			add_settings_field(
				'vulcan_minify_css',
				'Minify CSS',
				function( $args )
				{
					?>
						<label for="<?php echo $args['id']; ?>">
							<input type="checkbox" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" value="1" <?php checked( '1', get_option('vulcan_minify_css') ); ?> />
							Enable
						</label>
					<?php
				},
				'vulcan',
				'primary_settings',
				array(
					'id' => 'vulcan_minify_css'
				)
			);
			
			/*
			* Ajax Shortcodes Interface
			*/
			register_setting( 
				'vulcan_options', 
				'vulcan_interface_ajax_shortcodes' 
			);
			add_settings_field(
				'vulcan_interface_ajax_shortcodes',
				'Ajax Shortcodes Interface',
				function( $args )
				{
				?>
					<label for="<?php echo $args['id']; ?>">
						<input type="checkbox" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" value="1" <?php checked( '1', get_option('vulcan_interface_ajax_shortcodes') ); ?> />
						Enable
					</labe>
				<?php
				},
				'vulcan',
				'primary_settings',
				array(
					'id' => 'vulcan_interface_ajax_shortcodes'
				)
			);
			
		} );
		
		add_action( 'admin_menu', function()
		{
			add_menu_page(
				'DIF Design Theme Options',
				'Theme Options',
				'manage_options',
				'vulcan',
				function()
				{
					// check user capabilities
					if ( ! current_user_can( 'manage_options' ) )
					{
						return;
					}
					?>
					<div class="wrap">
						<h1><?= esc_html( get_admin_page_title() ); ?></h1>
						<p>Various options to toggle theme functinos and components.</p>
						<form action="options.php" method="post">
							<?php
							// output security fields for the registered setting "vulcan_options"
							settings_fields( 'vulcan_options' );
							// output setting sections and their fields
							// (sections are registered for "vulcan", each field is registered to a specific section)
							do_settings_sections( 'vulcan' );
							// output save settings button
							submit_button( 'Save Settings' );
							?>
						</form>
					</div>
					<?php
				},
				$this->themeUri . '/assets/media/vulcan-icon.png',
				2
			);
		} );
		
	}

	public function initVCElements( array $filenames )
	{
		add_action( 'vc_before_init', 'vc_before_init_actions' );
		function vc_before_init_actions()
		{
			foreach ( $filenames as $filename )
			{
				try
				{
					require_once( __DIR__ . $filename );
				}
				catch ( \Exception $e )
				{
					throw $e;
				}
			}
		}
	}

	public function initWidgets( array $obj )
	{
		add_action( 'widgets_init', 'register_vulcan_widgets' );
		function register_vulcan_widgets() {
			foreach ( $filenames as $filename )
			{
				try
				{
					require_once( __DIR__ . '/vulcan-widgets/' . $filename . '.php' );
					register_widget( $filename );
				}
				catch ( \Exception $e )
				{
					throw $e;
				}
			}
		}
	}

	public function enableModulesBasedOnThemeOptions()
	{
		if ( (bool)get_option('vulcan_interface_ajax_shortcodes') )
		{
			interfaces\AjaxShortcodes::enable();
		}
	}

	public function registerTaxonomies()
	{
		add_action( 'init', 'add_taxonomies_to_pages' );
		function add_taxonomies_to_pages()
		{
			register_taxonomy_for_object_type( 'post_tag', 'page' );
			register_taxonomy_for_object_type( 'category', 'page' );
		}
	}

	public function initFilters()
	{
		/*
		* Adds the option to hide Gravity form field labels.
		*/
		add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );
	}
	
	/*
	* Initializes theme styles.
	*/
	public function initStyles( string $readPath, string $writePath, string $filename )
	{
		add_action(
			'after_setup_theme',
			function() use( $readPath, $writePath, $filename )
			{
				$currentCss = $this->themePath . $writePath . $filename;
				
				$cssModulePaths = utils\AggregatorCss::getFiles(
					$this->themePath . $readPath,
					true
				);
				
				if ( file_exists( $currentCss ) )
				{
					$areNewFiles = utils\FileVersion::comparator( $currentCss, $cssModulePaths );
				}
				else
				{
					$areNewFiles = true;
				}
				
				if ( $areNewFiles )
				{
					$css = utils\AggregatorCss::agg(
						utils\AggregatorCss::getFiles(
							$this->themePath . $readPath,
							false
						)
					);
					if ( (bool)get_option('vulcan_minify_css') )
					{
						$css = utils\AggregatorCss::minify( $css );
					}
					utils\AggregatorCss::write(
						$css,
						$this->themePath . $writePath,
						$filename
					);
				}
			}
		);
		
		add_action(
			'update_option_' . 'vulcan_minify_css',
			function() use( $readPath, $writePath, $filename )
			{
				$css = utils\AggregatorCss::agg(
					utils\AggregatorCss::getFiles(
						$this->themePath . $readPath,
						false
					)
				);
				if ( (bool)get_option('vulcan_minify_css') )
				{
					$css = utils\AggregatorCss::minify( $css );
				}
				utils\AggregatorCss::write(
					$css,
					$this->themePath . $writePath,
					$filename
				);
			}
		); 
		
		add_action(
			'wp_enqueue_scripts',
			function() use( $writePath, $filename )
			{
				wp_enqueue_style(
					'vulcan-aggregate-minified-styles',
					$this->themeUri . $writePath . $filename,
					array( 'avada-stylesheet' )
				);
			},
			3
		);
		
	}

	/*
	* This needs to be moved to it's own method within $vulcan and made more robust.
	*/
	public function expose_mk_options()
	{
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
							utils\FileVersion::getVersion( $mainCSSPath )
						);
					},
					32
				);
			}
		);
	}
		
	
	/*
	* Initialize theme scripts
	*/
	public function initScripts()
	{

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

	}
	
}