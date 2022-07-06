<?php 

namespace Vulcan\lib\models\admin;

/**
 * @param (object) (Required) $pageSettings The Menu page settings.
 *
 * @param (string) (Required) $type The type of view to use for rendering.
 
 * @param (string) (Required) $title The formatted title of the section. Shown as the heading for the section.
 * 
 * @param (array) (Required) $fields [
 * 		'section' - (string) (required) The slug-name of the section of the settings page in which to show the box.
 * 		'group' - (string) (required) The unprefixed field group. Used to register the settings field.
 * 		'id' - (string) (required) The unprefixed field id. Used to register the settings field.
 * 		'type' - (string) (required) The type of field. Used to get the field view.
 * 		'description' - (string) (required) The field description.
 * ]
 * 
 */
/**
 * Class MenuSection
 *
 * @package Vulcan\models\admin
 */
class MenuSection {
	/**
	 * @var array
	 */
	public $fields;
	/**
	 * @var object
	 */
	private $settings;
	/**
	 * @var \stdClass
	 */
	private $pageSettings;

	/**
	 * MenuSection constructor.
	 *
	 * @param \stdClass $pageSettings
	 * @param string    $type
	 * @param string    $title
	 * @param array     $fields
	 *
	 * @throws \Vulcan\lib\utils\VulcanException
	 */
	public function __construct(
        \stdClass $pageSettings,
        string $type,
        string $title,
        array $fields
    ) {
		if ( 
			! $pageSettings || ! is_object( $pageSettings ) ||
         	! $title || ! is_string( $title ) ||
         	! isset( $fields ) || ! is_array( $fields ) 
		) {
            throw new \Vulcan\lib\utils\VulcanException( 'invalid_arguement' );
        }
		
		$this->pageSettings = $pageSettings;
		
		$this->settings = (object)array(
			'type' => $type,
			'title' => $title,
			'uc_title' => ucwords( $title )
		);
		
		$this->fields = $this->add_fields( $fields );
        
    }

	/**
	 *
	 */
	public function render() {
		$s = $this->settings;
		$ps = $this->pageSettings;
		\add_settings_section(
            $s->title,
            $s->uc_title,
            $this->get_view( $s->type, $s->uc_title ),
            $ps->slug
        );
	}

	/**
	 * @param $type
	 * @param $uc_title
	 *
	 * @return \Closure
	 */
	private function get_view( $type, $uc_title ) {
		return function() use( $type, $uc_title ) {
			$data = array(
				'title' => $uc_title,
			);
			$view = new \Vulcan\lib\views\View( 'admin', 'MenuSection' . ucwords($type), $data );
			echo $view->render();
		};
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 * @throws \Vulcan\lib\utils\VulcanException
	 */
	private function add_fields( array $fields ) {
		$temp = array();
        foreach ( $fields as $field ) {
            $temp[] = new MenuField(
                $this->pageSettings,
                $this->settings->title,
				$field['group'],
				$field['id'],
				$field['type'],
				$field['description']
            );
        }
        return $temp;
    }
}