<?php 

namespace Vulcan\lib\models\admin;

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
/**
 * Class MenuPage
 *
 * @package Vulcan\models\admin
 */
class MenuPage
{

	/**
	 * @var object
	 */
	private $settings;
	/**
	 * @var array
	 */
	public $sections;
	/**
	 * @var
	 */
	public $subPages;

	/**
	 * MenuPage constructor.
	 *
	 * @param array $settings
	 * @param array $sections
	 * @param array $subPages
	 *
	 * @throws \Vulcan\lib\utils\VulcanException
	 */
	public function __construct( array $settings, array $sections, array $subPages )
    {
        if (
			! $settings['title'] ||
			! $settings['capability'] || 
			! $settings['slug'] ||
			! $settings['icon'] || file_exists( $settings['icon'] ) ||
			! $settings['position'] ||
			! $settings['type']
		) {
            throw new \Vulcan\lib\utils\VulcanException( 'invalid_arguement' );
        }

        $this->settings = (object)array(
            'title' => $settings['title'],
            'capability' => $settings['capability'],
            'slug' => $settings['slug'],
            'icon' => $settings['icon'],
            'position' => $settings['position'],
            'type' => $settings['type']
        );
		
		if ( ! empty( $sections ) ) {
			$this->sections = $this->add_sections( $sections );
		}
		
		if ( ! empty( $subPages ) ) {
			$this->subPages = $this->add_subPages( $subPages );
		}

    }

	/**
	 *
	 */
	public function render()
    {
		$s = $this->settings;
		
		add_action(
			'admin_menu',
			function() use( $s )
			{
				add_menu_page(
					$s->title,
					$s->title,
					$s->capability,
					$s->slug,
					$this->get_view( $s->type, $s ),
					$s->icon,
					$s->position
				);
			}
		);
		
		add_action(
			'admin_init',
			function() {
				foreach ( $this->sections as $section ) {
					$section->render();
					foreach ( $section->fields as $field ) {
						$field->render();
					}
				}
			}
		);
		
    }

	/**
	 * @param string    $type
	 * @param \stdClass $s
	 *
	 * @return \Closure
	 */
	private function get_view( string $type, \stdClass $s ) {
        return function() use( $type, $s ) {
            // check user capabilities
            if ( ! current_user_can( $s->capability ) ) {
                return;
            }
			$data = array(
				'title' => $s->title,
                'slug' => $this->settings->slug
			);
			$view = new \Vulcan\views\View( 'admin', 'MenuPage' . ucwords($type), $data );
			echo $view->render();
        };
    }

	/**
	 * @param array $sections
	 *
	 * @return array
	 * @throws \Vulcan\lib\utils\VulcanException
	 */
	public function add_sections( array $sections ) {
        $temp = array();
        foreach ( $sections as $section ) {
            $temp[] = new MenuSection(
                $this->settings,
				$section['type'],
                $section['title'],
                $section['fields']
            );
        }
        return $temp;
    }

}