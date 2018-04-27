<?php

/*
* Vulcan Include Page
* Includes the content of a page and renders its shortcodes.
* This is very useful for templating and content abstraction.
*/
class vcVulcanIncludePage extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanIncludePage_mapping' ) );
        add_shortcode( 'vc_vulcanIncludePage', array( $this, 'vc_vulcanIncludePage_html' ) );
    }
    
    public function vc_vulcanIncludePage_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Include Page', 'text-domain'),
                'base' => 'vc_vulcanIncludePage',
                'description' => __('Includes the content of a page.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(
			
					array(
						"type" => "textfield",
						"heading" => __("Page Title", "mk_framework"),
						"param_name" => "page_title",
						"holder" => "span",
						"value" => "",
						"description" => __("", "mk_framework")
					)
                        
                )
            )
        );                                
        
    }
    
    public function vc_vulcanIncludePage_html( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
					'page_title' => '',
                ), 
                $atts
            )
        );
		
		if ( ! $page_title ) return;
		
		$raw_page = get_page_by_title( $page_title, 'OBJECT' );
		
		ob_start();
		echo do_shortcode( $raw_page->post_content );
		return ob_get_clean();
		
    }
     
}

new vcVulcanIncludePage();