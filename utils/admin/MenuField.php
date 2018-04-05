<?php namespace Vulcan\utils\admin;

class MenuField
{

    public function __construct( string $section, string $group, string $id, string $type, string $description )
    {
        $options_group = $GLOBALS['themeName'] . '_' . $group;
        $options_id = $GLOBALS['themeName'] . '_' . $id;
        $display_name = ucwords( str_replace( '_', ' ', $options_name) );
        register_setting(
            $options_group,
            $options_id
        );
        add_settings_field(
            $options_id,
            $display_name,
            self::callbackRouter( $args ),
            $GLOBALS['themeName'],
            $section,
            array(
                'section' => $section,
                'group' => $options_group,
                'id' => $options_id,
                'type' => $type,
                'description' => $description
            )
        );
    }

    private static function callbackRouter( array $args )
    {
        $t = $args['type'];
        switch ( $t )
        {
            case 'toggle':
                return self::render_toggle( $args );
                break;
        }
    }

    private static function render_toggle( $args)
    {
        return function() use( $args )
        {
            $id = $args['id'];
            $desc = $args['description'];
            ob_start();
            ?>
            <div class="slide_toggle">
                <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="1" <?php checked( '1', get_option( $id ) ); ?> >
                <label for="<?php echo $id; ?>"></label>
            </div>
            <p><?php echo esc_html( $desc ); ?></p>
            <?php
            echo ob_get_clean();
        };
    }

    
}