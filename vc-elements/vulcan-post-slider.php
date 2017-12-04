<?php

/*
* Vulcan Post Slider.
* Turns a category of posts into a hero slider.
*/
class vcVulcanPostSlider extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanPostSlider_mapping' ) );
		add_action( 'wp_enqueue_scripts', 'vulcan_post_slider_js', 10 );
		function vulcan_post_slider_js() {
			$path = get_stylesheet_directory_uri() . '/assets/js/vulcan-post-slider.js';
			wp_enqueue_script( 'vulcan-post-slider-js', $path, array(), vulcan_get_file_version($path), true);
		}
        add_shortcode( 'vc_vulcanPostSlider', array( $this, 'vc_vulcanPostSlider_html' ) );
    }
    
    public function vc_vulcanPostSlider_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Vulcan Post Slider', 'text-domain'),
                'base' => 'vc_vulcanPostSlider',
                'description' => __('Turns a category of posts into a hero slider.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(   
                         
                    array(
						'type'        => 'textfield',
						'heading'     => __( 'Categories', 'mk_framework' ),
						'param_name'  => 'category',
						'description' => __( 'Enter specific Categories to be used.', 'mk_framework' ),
					),
			
					array(
						"type" => "range",
						"heading" => __("Number of posts", "mk_framework") ,
						"param_name" => "num_posts",
						"value" => "3",
						"min" => "1",
						"max" => "12",
						"step" => "1",
						"unit" => "slides",
						"description" => __("Number of posts.", "mk_framework"),
					),

					array(
						"type" => "toggle",
						"heading" => __("Enable Excerpt", "mk_framework"),
						"param_name" => "enable_excerpt",
						"value" => "false",
						"description" => __("", "mk_framework"),
					),

					 array(
						"heading" => __("Order", 'mk_framework'),
						"description" => __("Designates the ascending or descending order of the 'orderby' parameter.", 'mk_framework'),
						"param_name" => "order",
						"value" => array(
							__("DESC (descending order)", 'mk_framework') => "DESC",
							__("ASC (ascending order)", 'mk_framework') => "ASC"
			
						),
						"type" => "dropdown"
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
    
    public function vc_vulcanPostSlider_html( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
                    'categories'   => '',
                    'num_posts' => '3',
					'order' => ''
                ), 
                $atts
            )
        );
		
		$query_args = array(
			'post_status' => 'publish',
		);

		if ( '' !== $categories )
		{
			$categories = explode( ',', $categories );
			$gc = array();
			foreach ( $categories as $grid_cat ) {
				array_push( $gc, $grid_cat );
			}
			$gc = implode( ',', $gc );
			$query_args['category_name'] = $gc;
		}

		// Order posts
		/*if ( null !== $orderby )
		{
			$query_args['orderby'] = $orderby;
		}*/
		
		$query_args['order'] = $order;
		
		$controls = array();

		$html = '<div id="Vulcan_Post_Slider" class="vulcan-post-slider">';
		
		$html .= '<div class="vulcan-slides-wrapper">';

		$my_query = new WP_Query( $query_args );

		$i = 0;
		while ( $my_query->have_posts() && $i < $num_posts ) {
			$i++;
			$my_query->the_post();
			$post_title = the_title( '', '', false );
			$post_id = $my_query->post->ID;

			//$content = $my_query->post->post_content;
			$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => 'slide-thumbnail' ) );
			$post_image = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'slide-fullimage' ) );
			
			$thumbControl = '<button class="slide-control" data-slide-index="' . $i . '"><div class="thumbnail-transform-wrapper">' . $post_thumbnail . '</div></button>';
			$controls[] = $thumbControl;

			$html .= 
				'<figure class="slide">' .
					'<div class="image-wrapper">' .
						$post_image .
						'<div class="image-shader"></div>' .
					'</div>' .
				 	'<figcaption class="slide-content">' .
						'<div class="slide-flex-wrapper">' .
							'<h2 class="slide-title">' . $post_title . ' ' . $ecv . '</h2>' .
							'<div class="slide-details">
								<div class="slide-postlink"><a href="' . get_permalink( $my_query->post->ID ) . '">FIND OUT MORE</a></div>
							</div>' .
						'</div>' .
					'</figure>' .
				'</figure>';
		}
		
		$html .= '</div>';
		
		$html .= '<nav class="slide-controls">';
		
		foreach ( $controls as $control )
		{
			$html .= (string)$control;
		}
		
		$html .= '</nav>';

		$html .= '</div>';

		wp_reset_query();     
         
        return $html;
         
    }
     
}

new vcVulcanPostSlider();