<?php namespace Vulcan\utils\admin;

/*
@param (array) (required) $settings [
    'title' - (string) (Required) The text to be displayed in the title tags of the page when the menu is selected. AND (string) (Required) The text to be used for the menu.

    'capability' - (string) (Required) The capability required for this menu to be displayed to the user.

    'slug' - (string) (Required) The slug name to refer to this menu by. Should be unique for this menu page and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().

    'icon' - (string) (Optional) The URL to the icon to be used for this menu. 
    * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'. 
    * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'. 
    * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
    Default value: ''

    'position' - (int) (Optional) The position in the menu order this one should appear.
    Default value: null

    'type' - (string) (required) The type of menu page to be rendered.
]
@param (array) (required) $sections [
    
]
*/
class MenuPage
{
    public $sections = null;
    private $settings = null;

    public function __construct( array $settings, array $sections )
    {
        if ( ! $args['title'] )
        {
            throw new WP_Error( 'invalid_arguement', $args['title'] );
        }
        if ( ! $args['capability'] )
        {
            throw new WP_Error( 'invalid_arguement', $args['capability'] );
        }
        if ( ! $args['slug'] )
        {
            throw new WP_Error( 'invalid_arguement', $args['slug'] );
        }
        if ( ! ( $args['icon'] && file_exists( $args['icon'] ) ) )
        {
            throw new WP_Error( 'invalid_arguement', $args['icon'] );
        }
        if ( ! $args['position'] )
        {
            throw new WP_Error( 'invalid_arguement', $args['position'] );
        }
        if ( ! $args['type'] )
        {
            throw new WP_Error( 'invalid_arguement', $args['type'] );
        }

        $this->settings = (object)array(
            'title' => $args['title'],
            'capability' => $args['capability'],
            'slug' => $args['slug'],
            'icon' => $args['icon'],
            'position' => $args['position'],
            'type' => $args['type']
        );

        $this->create_page( $this->settings );

        $this->sections = $this->add_sections( $sections );
    }

    private function create_page( object $s )
    {
        add_action( 'admin_menu', function()
		{
			add_menu_page(
				$s->title,
				$s->title,
				$s->capability,
				$s->slug,
				$this->callbackRouter( $s ),
				$s->icon,
				$s->position
			);
		}
		);
    }

    private function callbackRouter( object $s )
    {
        switch ( $s->type )
        {
            case 'standard':
                return $this->standard_page( $s );
                break;
        }
    }

    private function standard_page( object $s )
    {
        return function() use( $s )
        {
            // check user capabilities
            if ( ! current_user_can( $s->capability ) )
            {
                return;
            }
            ob_start();
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
                    do_settings_sections( $s->slug );
                    // output save settings button
                    submit_button( 'Save Settings' );
                    ?>
                </form>
            </div>
            <?php
            echo ob_get_clean();
        };
    }

    public function add_sections( array $sections )
    {
        $temp = array();
        foreach ( $sections as $section )
        {
            $temp[] = new utils\admin\MenuSection(
                $this->settings,
                $section['title'],
                $section['fields']
            );
        }
        return $sections;
    }

}