<?php namespace Vulcan\models\admin;

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
class MenuSection 
{
    public $fields;
	private $settings;
    public function __construct( object $pageSettings, string $type, string $title, array $fields )
    {
		if ( 
			! $s || ! is_object( $s ) ||
         	! $title || ! is_string( $title ) ||
         	! $fields || ! is_array( $fields ) 
		)
        {
            throw new \Vulcan\utils\VulcanException( 'invalid_arguement' );
        }
		
		$this->settings = (object)array(
			'type' => $type,
			'title' => $title,
			'uc_title' => ucwords( $title ),
			'fields' => $fields			
		);
		
		$this->fields = $this->add_fields( $fields );
		
        //$this->render();
        
    }
	
	public function render()
	{
		$s = $this->settings;
		add_settings_section(
            $s->title,
            $s->uc_title,
            $this->get_view( $s->type, $s->uc_title ),
            $pageSettings->slug
        );
	}
	
	private function get_view( $type, $uc_title )
	{
		return function() use( $type, $uc_title )
		{
			$data = array(
				'title' => $uc_title,
			);
			$view = new \Vulcan\views\View( 'MenuSection' . $type . '.php', $data );
			echo $view->render();
		};
	}

    private function add_fields( array $fields )
    {
		$temp = array();
        foreach ( $fields as $field )
        {
            $temp[] = new utils\admin\MenuField(
                $title,
				$field['group'],
				$field['id'],
				$field['type'],
				$field['description']
            );
        }
        return $temp;
    }
}