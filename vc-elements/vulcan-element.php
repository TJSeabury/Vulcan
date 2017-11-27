<?php
/*
* Element Description: VC Info Box
*/
 
// Element Class 
class vulcan_vc_post_slider extends WPBakeryShortCode
{
    function __construct() {
        add_action( 'init', array( $this, 'vulcan_vc_post_slider_mapping' ) );
        add_shortcode( 'vulcan_vc_post_slider', array( $this, 'vulcan_vc_post_slider_html' ) );
    }
    
    public function vc_infobox_mapping() {
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
                return;
        }

        // Map the block with vc_map()
        vc_map( 
        
            array(
                'name' => __('Post Slider', 'text-domain'),
                'base' => 'vulcan_vc_post_slider',
                'description' => __('Post slider with thumbnail controls.', 'text-domain'),
                'category' => __('Vulcan', 'text-domain'),
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(   
                    
                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'title-class',
                        'heading' => __( 'Title', 'text-domain' ),
                        'param_name' => 'title',
                        'value' => __( 'Default value', 'text-domain' ),
                        'description' => __( 'Box Title', 'text-domain' ),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'Custom Group',
                    ),  
                        
                    array(
                        'type' => 'textarea',
                        'holder' => 'div',
                        'class' => 'text-class',
                        'heading' => __( 'Text', 'text-domain' ),
                        'param_name' => 'text',
                        'value' => __( 'Default value', 'text-domain' ),
                        'description' => __( 'Box Text', 'text-domain' ),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'Custom Group',
                    )                   
                            
                )
            )
        );                                
            
    }
    
    public function vc_infobox_html( $atts ) {
        
        // Params extraction
        extract(
            shortcode_atts(
                array(
                    'title'   => '',
                    'text' => '',
                ), 
                $atts
            )
        );
            
        // Fill $html var with data
        $html = '
        <div class="vc-infobox-wrap">
            
            <h2 class="vc-infobox-title">' . $title . '</h2>
                
            <div class="vc-infobox-text">' . $text . '</div>
            
        </div>';
        
        return $html;
        
   }
     
}

new vcInfoBox(); 