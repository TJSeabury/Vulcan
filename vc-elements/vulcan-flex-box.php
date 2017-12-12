<?php

/*
* Vulcan Flex box.
* Flex your content, bruh. Crack open a cold one wit da boiz, bruh.
*/
class vcVulcanFlexbox extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanFlexbox_mapping' ) );
        add_shortcode( 'vc_vulcanFlexbox', array( $this, 'vc_vulcanFlexbox_html' ) );
    }
    
    public function vc_vulcanFlexbox_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Vulcan Flexbox', 'text-domain'),
                'base' => 'vc_vulcanFlexbox',
                'description' => __('Aedifico', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(   

					array(
						"type" => "textfield",
						"heading" => __("Extra class name", "mk_framework"),
						"param_name" => "el_class",
						"value" => "",
						"description" => __("If you wish to style particular content Flexbox differently, then use this field to add a class name and then refer to it in Custom CSS Shortcode or Masterkey Custom CSS option.", "mk_framework")
					)
                        
                )
            )
        );                                
        
    }
    
    public function vc_vulcanFlexbox_html( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
					'el_class' => '',
                ), 
                $atts
            )
        );
		
		ob_start();
		?>
		<div class="vulcan-Flexbox" >Aedifico</div>
		<?php
		return ob_get_clean();
		
    }
     
}

new vcVulcanFlexbox();