<?php namespace Vulcan\utils\admin;

/* 
* 
*/
class MenuPage
{

    public $page = null;
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
        if ( ! ( $args['callback'] && is_callable( $args['callback'] ) ) )
        {
            throw new WP_Error( 'invalid_arguement', $args['callback'] );
        }

        $this->settings = (object)array(
            'title' => $args['title'],
            'capability' => $args['capability'],
            'slug' => $args['slug'],
            'icon' => $args['icon'],
            'position' => $args['position'],
            'type' => $args['type'],
            'callback' => $args['callback']
        );

        $this->page = $this->create_page( $this->settings );

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

    public function add_sections()
    {

    }

}