<?php

/*
* Vulcan Post Slider.
* Turns a category of posts into a hero slider.
*/
class vcVulcanFacebookPage extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanFacebookPage_mapping' ) );
		add_action( 'theme_after_body_tag_start', array( $this, 'vc_vulcanIncludeFBSDK' ) );
        add_shortcode( 'vc_vulcanFacebookPage', array( $this, 'vc_vulcanFacebookPage_html' ) );
    }
    
    public function vc_vulcanFacebookPage_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Vulcan Facebook Page', 'text-domain'),
                'base' => 'vc_vulcanFacebookPage',
                'description' => __('Embeds a Facebook page feed.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(
			
					array(
						'type' => 'textfield',
						'heading' => __( 'Page Name', 'mk_framework' ),
						'param_name' => 'page_name',
						'value'	=> 'Facebook',
						'description' => __( 'The Title of the Facebook Page.', 'mk_framework' ),
					),
                         
                    array(
						'type' => 'textfield',
						'heading' => __( 'Page URL', 'mk_framework' ),
						'param_name' => 'href',
						'value'	=> 'https://www.facebook.com/facebook',
						'description' => __( 'The URL of the Facebook Page.', 'mk_framework' ),
					),
			
					array(
						"type" => "range",
						"heading" => __("Width", "mk_framework") ,
						"param_name" => "width",
						"value" => "340",
						"min" => "180",
						"max" => "500",
						"step" => "10",
						"unit" => "px",
						"description" => __("The pixel width of the plugin. Minimum is 180 & Maximum is 500.", "mk_framework"),
					),
			
					array(
						"type" => "range",
						"heading" => __("Height", "mk_framework") ,
						"param_name" => "height",
						"value" => "500",
						"min" => "70",
						"max" => "960",
						"step" => "10",
						"unit" => "px",
						"description" => __("The pixel height of the plugin. Minimum is 70.", "mk_framework"),
					),
					
					array(
						"type" => "toggle",
						"heading" => __("Timeline Tab", "mk_framework"),
						"param_name" => "enable_timeline",
						"value" => "true",
						"description" => __("", "mk_framework"),
					),
					array(
						"type" => "toggle",
						"heading" => __("Events Tab", "mk_framework"),
						"param_name" => "enable_events",
						"value" => "false",
						"description" => __("", "mk_framework"),
					),
					array(
						"type" => "toggle",
						"heading" => __("Messages Tab", "mk_framework"),
						"param_name" => "enable_messages",
						"value" => "false",
						"description" => __("", "mk_framework"),
					),
			
					array(
						"type" => "toggle",
						"heading" => __("Hide Cover Photo", "mk_framework"),
						"param_name" => "hide_cover",
						"value" => "false",
						"description" => __("Hide cover photo in the header.", "mk_framework"),
					),
			
					array(
						"type" => "toggle",
						"heading" => __("Show Profile Photos", "mk_framework"),
						"param_name" => "show_facepile",
						"value" => "true",
						"description" => __("Show profile photos when friends like this.", "mk_framework"),
					),
			
					array(
						"type" => "toggle",
						"heading" => __("Hide Custom Call-to-action Button", "mk_framework"),
						"param_name" => "hide_cta",
						"value" => "false",
						"description" => __("Hide the custom call to action button (if available)", "mk_framework"),
					),
					
					array(
						"type" => "toggle",
						"heading" => __("Enable Small Header", "mk_framework"),
						"param_name" => "small_header",
						"value" => "false",
						"description" => __("Use the small header instead.", "mk_framework"),
					),
			
					array(
						"type" => "toggle",
						"heading" => __("Adapt to Container Width", "mk_framework"),
						"param_name" => "adapt_container_width",
						"value" => "true",
						"description" => __("Try to fit inside the container width.", "mk_framework"),
					),

					array(
						"type" => "textfield",
						"heading" => __("Extra class name", "mk_framework"),
						"param_name" => "el_class",
						"value" => "",
						"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in Custom CSS Shortcode or Masterkey Custom CSS option.", "mk_framework")
					)
                        
                )
            )
        );                                
        
    }
	
	public function vc_vulcanIncludeFBSDK ()
	{
		$html = "<div id=\"fb-root\"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11';
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>";
		printf( $html );
	}
    
    public function vc_vulcanFacebookPage_html( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
					'page_name' => 'Facebook',
                    'href' => 'https://www.facebook.com/facebook',
					'width' => '340',
					'height' => '500',
					'enable_timeline' => 'true',
					'enable_events' => 'false',
					'enable_messages' => 'false',
					'hide_cover' => 'false',
					'show_facepile' => 'true',
					'hide_cta' => 'false',
					'small_header' => 'false',
					'adapt_container_width' => 'true',
                ), 
                $atts
            )
        );
		
		
		$tabs = array();
		if ( $enable_timeline )
		{
			$tabs[] = 'timeline';
		}
		if ( $enable_events )
		{
			$tabs[] = 'events';
		}
		if ( $enable_messages )
		{
			$tabs[] = 'messages';
		}
		$tabs = implode( ',', $tabs );
		
		/*
		* More info on this plugin:
		* https://developers.facebook.com/docs/plugins/page-plugin
		*/
		
		$html = '<article class="vulcan-facebook-page" style="max-width: ' . $width . 'px;">';
		
		$html .= 
		'<div ' .
		'class="fb-page" ' .
			
		// The URL of the Facebook Page.
		'data-href="' . $href . '" ' .
		
		// The pixel width of the plugin. Min. is 180 & Max. is 500.
		'data-width="' . $width . '" '.
		
		// The pixel height of the plugin. Min. is 70.
		'data-height="' . $height . '" ' .
		
		// Tabs to render i.e. timeline, events, messages.
		// Use a comma-separated list to add multiple tabs, i.e. timeline, events.	
		'data-tabs="' . $tabs . '" ' .
		
		// Hide cover photo in the header.	
		'data-hide-cover="' . $hide_cover . '" ' .
		
		// Show profile photos when friends like this.	
		'data-show-facepile="' . $show_facepile . '" ' .
			
		'data-hide-cta="' . $hide_cta . '" ' .
			
		// Use the small header instead.
		'data-small-header="' . $small_header . '" ' .
			
		// Try to fit inside the container width.
		'data-adapt-container-width="' . $adapt_container_width . '" ' .
		
		'>' .		
			
			'<blockquote ' .
			
			// The URL of the Facebook Page.
			'cite="' . $href . '" ' .
			'class="fb-xfbml-parse-ignore">' .
			
				// The URL of the Facebook Page and Name.
				'<a href="' . $href . '">' . $page_name . '</a>' .
				
			'</blockquote>' .
		'</div>';

		$html .= '</article>';
        
        return $html;
         
    }
     
}

new vcVulcanFacebookPage();