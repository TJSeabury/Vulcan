<?php namespace Vulcan\models\admin;

/**
 * @param (array) (required) $settings [
 *     	'title' - (string) (Required) The text to be displayed in the title tags of the page when the menu is selected.
 * 		AND (string) (Required) The text to be used for the menu.
 * 
 *     	'capability' - (string) (Required) The capability required for this menu to be displayed to the user.
 * 
 *     	'slug' - (string) (Required) The slug name to refer to this menu by.
 * 		Should be unique for this menu page and only include lowercase alphanumeric, dashes, 
 * 		and underscores characters to be compatible with sanitize_key().
 * 
 *     	'icon' - (string) (Optional) The URL to the icon to be used for this menu. 
 *     	* Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme. 
 * 		This should begin with 'data:image/svg+xml;base64,'. 
 *     	* Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'. 
 *     	* Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
 *     	Default value: ''
 * 
 *     	'position' - (int) (Optional) The position in the menu order this one should appear.
 *     	Default value: null
 * 
 *     	'type' - (string) (required) The type of menu page to be rendered.
 * ]
 *
 * @param (array) (required) $sections [
 *     
 * ]
 *
 */
class MenuPage
{
    public $sections;
    private $settings;

    public function __construct( array $settings, array $sections )
    {
        if (
			! $settings['title'] ||
			! $settings['capability'] || 
			! $settings['slug'] ||
			! $settings['icon'] || file_exists( $settings['icon'] ) ||
			! $settings['position'] ||
			! $settings['type']
		)
        {
            throw new \Vulcan\utils\VulcanException( 'invalid_arguement' );
        }

        $this->settings = (object)array(
            'title' => $settings['title'],
            'capability' => $settings['capability'],
            'slug' => $settings['slug'],
            'icon' => $settings['icon'],
            'position' => $settings['position'],
            'type' => $settings['type']
        );
		
		$this->sections = $this->add_sections( $sections );

    }

    public function render()
    {
		$s = $this->settings;
		add_menu_page(
			/*'Vulcan Options',
			'Vulcan Options',
			'manage_options',
			'vulcan',
			'',
			2*/
			$s->title,
			$s->title,
			$s->capability,
			$s->slug,
			$this->get_view( $s->type, $s ),
			$s->icon,
			$s->position
		);
    }

    private function get_view( string $type, \stdClass $s )
    {
        return function() use( $type, $s )
        {
            // check user capabilities
            if ( ! current_user_can( $s->capability ) )
            {
                return;
            }
			$data = array(
				'title' => 'My title',
				'content' => 'My content'
			);
			$view = new \Vulcan\views\View( $type . '.php', $data );
			echo $view->render();
        };
    }

    public function add_sections( array $sections )
    {
        $temp = array();
        foreach ( $sections as $section )
        {
            $temp[] = new \Vulcan\models\admin\MenuSection(
                $this->settings,
				$section['type'],
                $section['title'],
                $section['fields']
            );
        }
        return $temp;
    }

}