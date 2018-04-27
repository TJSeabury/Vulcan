<?php namespace Vulcan\utils\admin;

/**
 * @param (object) (Required) $s The Menu page settings.
 * 
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
    public $fields = null;
    public function __construct( object $s, string $title, array $fields )
    {
		if ( 
			! $s || ! is_object( $s ) ||
         	! $title || ! is_string( $title ) ||
         	! $fields || ! is_array( $fields ) 
		)
        {
            throw new \Vulcan\utils\VulcanException( 'invalid_arguement' );
        }
		
		$uc_title = ucwords( $title );
		
        add_settings_section(
            $title,
            $uc_title,
            function() use( $uc_title )
            {
                $html = <<<HTML
                <div class="section-header">
                    <h2>{$uc_title}</h2>
                </div>
HTML;
                echo html;
            },
            $s->slug
        );
        $this->fields = $this->add_fields( $fields );
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