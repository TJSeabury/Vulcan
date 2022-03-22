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
	public function getImageAverageColor( $sourceURL ) {
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
	public function get_header_view( string $view ) {
		$path = $this->themePath . '/views/header/' . $view . '.php';
		ob_start();
		include $path ;
		$header = ob_get_clean();
		return $header;
	}


	/**
	 * Enqueues required view styles based upon theme options.
	 */
	public function set_view_styles() {
		
	}

	public function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			echo '<section class="vulcan-options-section">';
			if ( $section['title'] )
				echo "<h2>{$section['title']}</h2>\n";

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;
			echo '<table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
			echo '</section>';
		}
	}


	/**
	 * Initializes Wordpress admin theme menu.
	 */
	public function initAdmin() {
		add_action(
			'admin_enqueue_scripts',
			function()
			{
				$path = $this->themeUri . '/assets/admin/css/admin.css';
				wp_enqueue_style(
					'Vulcan-Admin-Styles',
					$path,
					array(),
					utils\FileTools::getVersion( $path )
				);
			}
		);
		$group = 'primary';
		try
        {
	        $themeOptions = new models\admin\MenuPage(
		        [
			        'title' => 'Vulcan Options',
			        'capability' => 'manage_options',
			        'slug' => 'vulcan',
			        'icon' => $this->themeUri . '/assets/media/vulcan-icon-tiny.png',
			        'position' => 2,
			        'type' => 'general'
                ],
		        [
			        [
				        'type' => 'general',
				        'title' => 'General Options',
				        'fields' => [
                            [
                                'group' => $group,
                                'id' => 'vulcan_theme_mode',
                                'type' => 'toggle',
                                'description' => 'The theme mode, i.e., production or development.'
                            ],
					        [
						        'group' => $group,
						        'id' => 'vulcan_primary_color',
						        'type' => 'color',
						        'description' => 'The primary theme color.'
					        ],
					        [
						        'group' => $group,
						        'id' => 'vulcan_minify_css',
						        'type' => 'toggle',
						        'description' => 'Enable CSS minification?'
                            ],
                            [
						        'group' => $group,
						        'id' => 'vulcan_interface_ajax_shortcodes',
						        'type' => 'toggle',
						        'description' => 'Enable rendering shortcodes over ajax?'
					        ]
                        ]
                    ],
			        [
				        'type' => 'general',
				        'title' => 'Special Options',
				        'fields' => [
					        [
						        'group' => $group,
						        'id' => 'test_text_2',
						        'type' => 'text',
						        'description' => 'A test field to demonstrate the text view.'
                            ],
					        [
						        'group' => $group,
						        'id' => 'test_toggle2',
						        'type' => 'toggle',
						        'description' => 'A test field to demonstrate the toggle view.'
                            ],
					        [
						        'group' => $group,
						        'id' => 'test_toggle3',
						        'type' => 'toggle',
						        'description' => 'A test field to demonstrate the toggle view.'
                            ]
                        ]
                    ],
                    [
                        'type' => 'general',
                        'title' => 'Theme Modules',
                        'fields' => [

                        ]
                    ],
                    [
                        'type' => 'general',
                        'title' => 'Theme JSON',
                        'fields' => [
                            [
                                'group' => $group,
                                'id' => 'theme_json_test',
                                'type' => 'json',
                                'description' => 'JSON test.'
                            ]
                        ]
                    ]
                ],
		        []
	        );

	        $themeOptions->render();
        }
        catch ( utils\VulcanException $e )
        {
            echo $e;
        }

		
	}

	public function initVCElements( array $filenames ) {
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

	public function initBuilderModules() {
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

	public function initWidgets( array $Widgets ) {
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

	public function enableModulesBasedOnThemeOptions() {
		if ( (bool)get_option('vulcan_interface_ajax_shortcodes') )
		{
			interfaces\ajax\AjaxShortcodes::enable();
		}
	}

	public function registerTaxonomies() {
		add_action( 'init', 'add_taxonomies_to_pages' );
		function add_taxonomies_to_pages()
		{
			register_taxonomy_for_object_type( 'post_tag', 'page' );
			register_taxonomy_for_object_type( 'category', 'page' );
		}
	}

	public function initFilters() {

		/*
		* Custom admin footers.
		*/
		add_filter( 
			'admin_footer_text', 
			function()
			{
				$wordpress = '<a href="http://www.wordpress.org" target="_blank">WordPress</a>';
				$author = '<a href="https://marketmentors.com" target="_blank">Market Mentors</a>';

				$taglines = array(
					"Your custom $wordpress, and theme, designed and developed by $author.",
					"At $author, we ensure that all $wordpress themes are raised free-range and organic.",
					"Your custom $wordpress, sourced and crafted from local materials by $author.",
					"Gluten free $wordpress, baked with love from $author.",
					"Harder, better, faster, blogger. Your custom $wordpress by $author.",
					"\"Damn it man! I'm a $wordpress engineer, not a doctor!\" — someo-ne at $author probably.",
					"$author and the Masters of the Cyberverse: \"By the will of $wordpress, I have the power!\""
				);

				$rn = random_int( 0, count( $taglines ) - 1 );

				ob_start();
				?>
				<div class="marketmentors_admin_footer">
					<p><?php echo $taglines[ $rn ]; ?></p>
				</div>
				<?php
				echo ob_get_clean();
			}
		);

		/*
		* Adds the option to hide Gravity Forms field labels.
		*/
		add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );
	}
	
	/*
	* Initializes theme styles.
	*/
	public function initStyles( string $readPath, string $writePath, string $filename ) {
		add_action(
			'after_setup_theme',
			function() use( $readPath, $writePath, $filename ) {
				$currentCss = $this->themePath . $writePath . $filename;
				
				$cssModulePaths = utils\FileTools::getFiles(
					$this->themePath . $readPath,
					array( 'php', 'css' ),
					true
				);
				
				if ( file_exists( $currentCss ) ) {
					$areNewFiles = utils\FileTools::comparator( $currentCss, $cssModulePaths );
				} else {
					$areNewFiles = true;
				}
				
				if ( $areNewFiles ) {
					$css = utils\FileTools::agg(
						utils\FileTools::getFiles(
							$this->themePath . $readPath,
							array( 'php', 'css' ),
							false
						)
					);
					if ( (bool)get_option('vulcan_minify_css') ) {
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
			function() use( $readPath, $writePath, $filename ) {
				$css = utils\FileTools::agg(
					utils\FileTools::getFiles(
						$this->themePath . $readPath,
						array( 'php', 'css' ),
						false
					)
				);
				if ( (bool)get_option('vulcan_minify_css') ) {
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
			function() use( $writePath, $filename ) {
				wp_enqueue_style(
					'vulcan-aggregate-minified-styles',
					$this->themeUri . $writePath . $filename
				);
			},
			3
		);
		
	}


	public function expose_mk_options() {
		$path = __DIR__ . '/assets/css/mk-options.css';
		add_action(
			'init',
			function() use( $path ) {
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
				if ( (int)get_option('global_assets_timestamp') > $timestamp ) {
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
	<?php for ( $f = 0; $f < count( $mko['fonts'] ); ++$f ) { ?>
	--mk-font-<?php echo $f; ?>: <?php echo $mko['fonts'][$f]['fontFamily']; ?>;
	<?php } ?>
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
	public function initScripts( array $paths ) {
		foreach( $paths as $path ) {
			$filePaths = utils\FileTools::getFiles(
				$this->themePath . $path,
				array( 'js' ),
				true
			);
			foreach( $filePaths as $fpath ) {
				$name = null;
				$priority = 10;
				$metaHeader = get_file_data(
					$fpath,
					array(
						'Name' => 'Name',
						'Priority' => 'Priority'
						)
				);
				if ( $metaHeader['Name'] ) {
					$name = $metaHeader['Name'];
				} else {
					$fn = pathinfo( $fpath );
					if ( $fn ) {
						$name = $fn['filename'];
					}
				}
				if ( $metaHeader['Priority'] ) {
					$priority = $metaHeader['Priority'];
				}
				$fpath = utils\FileTools::get_url_from_path( $this, $fpath );
				add_action(
					'wp_enqueue_scripts',
					function() use( $name, $fpath ) {
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

    public function forcePlugins() {
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
            $plugins = [
                /* [
                    'name'               => 'Beaver Builder', // The plugin name.
                    'slug'               => 'bb-plugin', // The plugin slug (typically the folder name).
                    'source'             => get_template_directory() . '/vendor/bb-plugin.zip', // The plugin source.
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required.
                    'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
                    'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                    'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
                    'external_url'       => '', // If set, overrides default API URL and points to an external URL.
                    'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
                ], */
                [
                    'name' => 'Imsanity',
                    'slug' => 'imsanity',
                    'required' => true,
                    'force_activation'   => true,
                    'force_deactivation' => true,
                ],
                [
                    'name' => 'Webp Express',
                    'slug' => 'webp-express',
                    'required' => true,
                    'force_activation'   => true,
                    'force_deactivation' => true,
                ],
                [
                    'name' => 'Disable Comments',
                    'slug' => 'disable-comments',
                    'required' => true,
                    'force_activation'   => true,
                    'force_deactivation' => true,
                ],
                [
                    'name' => 'Yoast SEO',
                    'slug' => 'wordpress-seo',
                    'required' => true,
                    'force_activation'   => true,
                    'force_deactivation' => true,
                ],

            ];

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
                'id'           => '_vulcan',               // Unique ID for hashing notices for multiple instances of TGMPA.
                'default_path' => '',                      // Default absolute path to bundled plugins.
                'menu'         => 'tgmpa-install-plugins', // Menu slug.
                'parent_slug'  => 'themes.php',            // Parent menu slug.
                'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                'has_notices'  => true,                    // Show admin notices or not.
                'dismissable'  => false,                   // If false, a user cannot dismiss the nag message.
                'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => true,                    // Automatically activate plugins after installation or not.
                'message'      => '',                      // Message to output right before the plugins table.
                'strings'      => array(
                    'page_title'                      => __( 'Install Required Plugins', '_vulcan' ),
                    'menu_title'                      => __( 'Install Plugins', '_vulcan' ),
                    /* translators: %s: plugin name. */
                    'installing'                      => __( 'Installing Plugin: %s', '_vulcan' ),
                    /* translators: %s: plugin name. */
                    'updating'                        => __( 'Updating Plugin: %s', '_vulcan' ),
                    'oops'                            => __( 'Something went wrong with the plugin API.', '_vulcan' ),
                    'notice_can_install_required'     => _n_noop(
                        /* translators: 1: plugin name(s). */
                        'This theme requires the following plugin: %1$s.',
                        'This theme requires the following plugins: %1$s.',
                        '_vulcan'
                    ),
                    'notice_can_install_recommended'  => _n_noop(
                        /* translators: 1: plugin name(s). */
                        'This theme recommends the following plugin: %1$s.',
                        'This theme recommends the following plugins: %1$s.',
                        '_vulcan'
                    ),
                    'notice_ask_to_update'            => _n_noop(
                        /* translators: 1: plugin name(s). */
                        'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                        'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                        '_vulcan'
                    ),
                    'notice_ask_to_update_maybe'      => _n_noop(
                        /* translators: 1: plugin name(s). */
                        'There is an update available for: %1$s.',
                        'There are updates available for the following plugins: %1$s.',
                        '_vulcan'
                    ),
                    'notice_can_activate_required'    => _n_noop(
                        /* translators: 1: plugin name(s). */
                        'The following required plugin is currently inactive: %1$s.',
                        'The following required plugins are currently inactive: %1$s.',
                        '_vulcan'
                    ),
                    'notice_can_activate_recommended' => _n_noop(
                        /* translators: 1: plugin name(s). */
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
                    /* translators: 1: plugin name. */
                    'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', '_vulcan' ),
                    /* translators: 1: plugin name. */
                    'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', '_vulcan' ),
                    /* translators: 1: dashboard link. */
                    'complete'                        => __( 'All plugins installed and activated successfully. %1$s', '_vulcan' ),
                    'dismiss'                         => __( 'Dismiss this notice', '_vulcan' ),
                    'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', '_vulcan' ),
                    'contact_admin'                   => __( 'Please contact the administrator of this site for help.', '_vulcan' ),

                    'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
                ),
               
            );

            tgmpa( $plugins, $config );
        }
    }

    public function checkUpdates() {
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
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    public function setup() {
        add_action(
            'after_setup_theme',
            function() {
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
        );
            
    }

    /**
     * Register widget areas.
     *
     * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
     */
    public function initSidebars() {
        add_action(
            'widgets_init',
            function() {
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
        );
    }
	
}