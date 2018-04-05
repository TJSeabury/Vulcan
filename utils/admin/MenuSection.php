<?php namespace Vulcan\utils\admin;

class MenuSection 
{
    public function __construct( object $s, string $title, array $fields )
    {
        add_settings_section(
            $title,
            ucwords( $title ),
            function() use( $title )
            {
                ob_start();
                ?>
                <div class="section-header">
                    <h2><?php echo ucwords( $title ); ?></h2>
                </div>
                <?php
                echo ob_get_clean();
            },
            $s->slug
        );
    }
}