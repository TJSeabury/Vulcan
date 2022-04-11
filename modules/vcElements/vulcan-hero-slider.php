<?php 

namespace Vulcan\vcElements;

/*
* Vulcan Post Slider.
* Turns a category of posts into a hero slider.
*/
class vcVulcanHeroSlider extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanHeroSlider_mapping' ) );
		add_action( 'wp_enqueue_scripts', 'vulcan_hero_slider_js', 10 );
		function vulcan_hero_slider_js() {
			$path = get_stylesheet_directory_uri() . '/assets/js/vulcan-hero-slider.js';
			wp_enqueue_script( 'vulcan-hero-slider-js', $path, array(), utils\FileTools::getVersion($path), true);
		}
        add_shortcode( 'vc_vulcanHeroSlider', array( $this, 'vc_vulcanHeroSlider_html' ) );
    }
    
    public function vc_vulcanHeroSlider_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Hero Slider', 'text-domain'),
                'base' => 'vc_vulcanHeroSlider',
                'description' => __('Turns a selection of images into a hero slider.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(   
			
					array(
						'type' => 'attach_images',
						'heading' => 'Select Images',
						'param_name' => 'images',
						"description" => __("", "mk_framework"),
					),
			
					array(
						"type" => "range",
						"heading" => __("Interval", "mk_framework") ,
						"param_name" => "interval",
						"value" => "7000",
						"min" => "1000",
						"max" => "15000",
						"step" => "100",
						"unit" => "ms",
						"description" => __("The time each slide is shown, in miliseconds.", "mk_framework"),
					),
			
					array(
						"type" => "range",
						"heading" => __("Speed", "mk_framework") ,
						"param_name" => "speed",
						"value" => "1000",
						"min" => "100",
						"max" => "10000",
						"step" => "100",
						"unit" => "ms",
						"description" => __("The total time for each slide transition, in ms.", "mk_framework"),
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
    
    public function vc_vulcanHeroSlider_html( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
					'images' => '',
					'interval' => '7000',
					'speed' => '1000'
                ), 
                $atts
            )
        );
		
		$images = (
			function ( $im )
			{
				$urls = array();
				foreach ( $im as $i )
				{
					$urls[] = wp_get_attachment_url( $i );
				}
				return $urls;
			}
		)( explode( ',', $images ) );
		
		ob_start();
		?>
		<figure id="marketmentors-hero-slider" class="HeroSlider" data-dhs-slides='<?php echo json_encode( $images ); ?>' ></figure>
		<?php
		return ob_get_clean();
		
    }
     
}

new vcVulcanHeroSlider();