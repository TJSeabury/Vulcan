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
		$GLOBALS['themeName'] = 'vulcan';
		/* 
		 * Here we set the date of the first install as a DB option.
		 */
		if ( ! get_option( 'vulcan_first_published_year' ) )
		{
			update_option( 'vulcan_first_published_year', (int)date('Y') );
		}
	}
	
	public function getBasePath()
	{
		return $this->themePath;
	}

	public function getBaseUri()
	{
		return $this->themeUri;
	}

	public function get_the_copyright_years()
	{
		$first_published_year = get_option( 'vulcan_first_published_year' );
		$current_year = date('Y');

		if ( (int)$first_published_year === (int)$current_year )
		{
			return $current_year;
		}
		else if ( (int)$current_year - (int)$first_published_year === 1 )
		{
			return "$first_published_year, $current_year";
		}
		else
		{
			return "$first_published_year - $current_year";
		}
	}
	
	public function setMaxImageResolutionAndQuality( int $width, int $height, int $quality )
	{
		add_filter( 
			'jpeg_quality', 
			function( $arg ) use( $quality )
			{
				return $quality;
			}
		);
		
		add_image_size( 'standard', $width, $height, false );

		function replace_uploaded_image($image_data) {
			// if there is no standard image : return
			if ( ! isset( $image_data['sizes']['standard'] ) )
			{
				return $image_data;
			}

			// paths to the uploaded image and the standard image
			$upload_dir = wp_upload_dir();
			$uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];

			// $standard_image_location = $upload_dir['path'] . '/'.$image_data['sizes']['standard']['file']; // ** This only works for new image uploads - fixed for older images below.
			$current_subdir = substr($image_data['file'],0,strrpos($image_data['file'],"/"));
			$standard_image_location = $upload_dir['basedir'] . '/'.$current_subdir.'/'.$image_data['sizes']['standard']['file'];

			// delete the uploaded image
			unlink( $uploaded_image_location );

			// rename the standard image
			rename( $standard_image_location, $uploaded_image_location );

			// update image metadata and return them
			$image_data['width'] = $image_data['sizes']['standard']['width'];
			$image_data['height'] = $image_data['sizes']['standard']['height'];
			unset($image_data['sizes']['standard']);

			return $image_data;
		}
		add_filter(
			'wp_generate_attachment_metadata',
			'\Vulcan\replace_uploaded_image'
		);
	}
	

	/**
	 * @link https://stackoverflow.com/questions/3468500/detect-overall-average-color-of-the-picture
	 */
	public function getImageAverageColor( $sourceURL )
	{
		$image = imagecreatefromjpeg( $sourceURL );
		$scaled = imagescale( $image, 1, 1, IMG_BICUBIC );
		$index = imagecolorat( $scaled, 0, 0 );
		$rgb = imagecolorsforindex( $scaled, $index );
		$red = round( round( ( $rgb['red'] / 0x33 ) ) * 0x33 );
		$green = round( round( ( $rgb['green'] / 0x33 ) ) * 0x33 );
		$blue = round( round( ( $rgb['blue'] / 0x33 ) ) * 0x33 );
		return "#$red$green$blue"; 
	}

	/**
	 * Renders the header view.
	 * @param string $view The name of the view.
	 * @return string The header view HTML.
	 */
	public function get_header_view( string $view )
	{
		$path = $this->themePath . '/views/header/' . $view . '.php';
		ob_start();
		include $path ;
		$header = ob_get_clean();
		return $header;
	}


	/**
	 * Enqueues required view styles based upon theme options.
	 */
	public function set_view_styles()
	{
		
	}


	/**
	 * Initializes Wordpress admin theme menu.
	 */
	public function initAdmin()
	{
		$themeOptions = new \Vulcan\models\admin\MenuPage(
			array(
				'Vulcan Options',
				'manage_options',
				'vulcan',
				$this->themeUri . '/assets/media/vulcan-icon-tiny.png',
				2,
				'General'
			),
			array(
				'General',
				'General Options',
				array(
					'general',
					'Test Toggle',
					'test_toggle',
					'Toggle',
					'A test field to demonstrate the toggle view.'
				)
			)
		);
	}
	
	public function OLD_initAdmin()
	{
		add_action( 'admin_init', function()
	    {
			add_settings_section( 
				'primary_settings',
				'Primary Settings',
				function( $args )
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
					</label>
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
			
		}
		);
		
		add_action( 'admin_menu', function()
		{
			add_menu_page(
				'Vulcan Options',
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
						<p>Various options to toggle theme functions and components.</p>
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
				$this->themeUri . '/assets/media/vulcan-icon-tiny.png',
				2
			);
		}
		);
		
	}

	public function initVCElements( array $filenames )
	{
		add_action(
			'vc_before_init',
			function() use( $filenames )
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
		);
	}

	public function initBuilderModules()
	{
		return function()
		{
			if ( class_exists( 'FLBuilder' ) ) {
				/*
				* Register custom modules
				*/
				require_once __DIR__ . '/modules/vulcan-button/vulcan-button.php';
				
				/*
				* Register custom fields
				*/
				add_filter(
					'fl_builder_custom_fields',
					function( $fields )
					{
						$fields['vulcan-toggle'] = __DIR__ . '/modules/vulcan-fields/vulcan-toggle.php';
						$fields['vulcan-range'] = __DIR__ . '/modules/vulcan-fields/vulcan-range.php';
						return $fields;
					}
				);
				
				/*
				* Enqueue custom field assets
				*/
				$path = $this->themeUri;
				add_action(
					'wp_enqueue_scripts',
					function() use( $path )
					{
						if ( \FLBuilderModel::is_builder_active() ) {
							wp_enqueue_style( 'vulcan-fields', $path . '/modules/vulcan-fields/fields.css', array(), '' );
							wp_enqueue_script( 'vulcan-fields', $path . '/modules/vulcan-fields/fields.js', array(), '', true );
						}
					}
				);
			}
		};
	}

	public function initWidgets( array $Widgets )
	{
		add_action(
			'widgets_init',
			function() use( $Widgets ) {
				foreach ( $Widgets as $Widget )
				{
					try
					{
						require_once( __DIR__ . '/vulcan-widgets/' . $Widget . '.php' );
						register_widget( $Widget );
					}
					catch ( \Exception $e )
					{
						throw $e;
					}
				}
			}
		);
		
	}

	public function enableModulesBasedOnThemeOptions()
	{
		if ( (bool)get_option('vulcan_interface_ajax_shortcodes') )
		{
			interfaces\ajax\AjaxShortcodes::enable();
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
		* Custom admin footers.
		*/
		
		add_filter( 
			'admin_footer_text', 
			function()
			{
				$wordpress = '<a href="http://www.wordpress.org" target="_blank">WordPress</a>';
				$difdesign = '<a href="https://difdesign.com" target="_blank">DIF Design</a>';

				$taglines = array(
					"Your custom $wordpress, and theme, designed and developed by $difdesign.",
					"At $difdesign, we ensure that all $wordpress themes are raised free-range and organic.",
					"Your custom $wordpress, sourced and crafted from local materials by $difdesign.",
					"Gluten free $wordpress, baked with love from $difdesign.",
					"Harder, better, faster, blogger. Your custom $wordpress by $difdesign.",
					"\"Damn it man! I'm a $wordpress engineer, not a doctor!\" — someone at $difdesign probably.",
					"$difdesign and the Masters of the Cyberverse: \"By the power of $wordpress! I have the power!\""
				);

				$rn = random_int( 0, count( $taglines ) - 1 );

				ob_start();
				?>
				<div class="difdesign_admin_footer">
					<p><?php echo $taglines[ $rn ]; ?></p>
				</div>
				<?php
				echo ob_get_clean();
			}
		);

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
				
				$cssModulePaths = utils\FileTools::getFiles(
					$this->themePath . $readPath,
					array( 'php', 'css' ),
					true
				);
				
				if ( file_exists( $currentCss ) )
				{
					$areNewFiles = utils\FileTools::comparator( $currentCss, $cssModulePaths );
				}
				else
				{
					$areNewFiles = true;
				}
				
				if ( $areNewFiles )
				{
					$css = utils\FileTools::agg(
						utils\FileTools::getFiles(
							$this->themePath . $readPath,
							array( 'php', 'css' ),
							false
						)
					);
					if ( (bool)get_option('vulcan_minify_css') )
					{
						$css = utils\FileTools::minify( $css );
					}
					utils\FileTools::write(
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
				$css = utils\FileTools::agg(
					utils\FileTools::getFiles(
						$this->themePath . $readPath,
						array( 'php', 'css' ),
						false
					)
				);
				if ( (bool)get_option('vulcan_minify_css') )
				{
					$css = utils\FileTools::minify( $css );
				}
				utils\FileTools::write(
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
					$this->themeUri . $writePath . $filename
				);
			},
			3
		);
		
	}


	public function expose_mk_options()
	{
		$path = __DIR__ . '/assets/css/mk-options.css';
		add_action(
			'init',
			function() use( $path )
			{
				$timestamp = 0;
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
	<?php
	for ( $f = 0; $f < count( $mko['fonts'] ); ++$f )
	{
	?>
	--mk-font-<?php echo $f; ?>: <?php echo $mko['fonts'][$f]['fontFamily']; ?>;
	<?php
	}
	?>
}
					<?php
					$css = ob_get_clean();
					$f = fopen( $path, 'wb' );
					fwrite( $f, $css );
					fclose( $f );
				}
				add_action( 'wp_enqueue_scripts',
					function() use( $path )
					{
						wp_enqueue_style(
							'MK-Options-Exposed',
							$path,
							array(),
							utils\FileTools::getVersion( $path )
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
	public function initScripts( array $paths )
	{
		foreach( $paths as $path )
		{
			$filePaths = utils\FileTools::getFiles(
				$this->themePath . $path,
				array( 'js' ),
				true
			);
			foreach( $filePaths as $fpath )
			{
				$name = null;
				$priority = 10;
				$metaHeader = get_file_data(
					$fpath,
					array(
						'Name' => 'Name',
						'Priority' => 'Priority'
						)
				);
				if ( $metaHeader['Name'] )
				{
					$name = $metaHeader['Name'];
				}
				else
				{
					$fn = pathinfo( $fpath );
					if ( $fn )
					{
						$name = $fn['filename'];
					}
				}
				if ( $metaHeader['Priority'] )
				{
					$priority = $metaHeader['Priority'];
				}
				$fpath = utils\FileTools::get_url_from_path( $this, $fpath );
				add_action(
					'wp_enqueue_scripts',
					function() use( $name, $fpath )
					{
						wp_enqueue_script(
							$name,
							$fpath,
							array(),
							utils\FileTools::getVersion( $fpath ),
							true
						);
					},
					$priority
				);
			}
		}
		
	}
	
}