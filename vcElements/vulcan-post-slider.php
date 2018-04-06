<?php namespace Vulcan\vcElements;

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
			wp_enqueue_script( 'vulcan-post-slider-js', $path, array(), utils\FileTools::getVersion($path), true);
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
                'name' => __('Post Slider', 'text-domain'),
                'base' => 'vc_vulcanPostSlider',
                'description' => __('Turns a category of posts into a hero slider.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(   
                         
                    array(
						'type'        => 'textfield',
						'heading'     => __( 'Categories', 'mk_framework' ),
						'param_name'  => 'categories',
						'value' => '',
						'description' => __( 'Enter specific Categories to be used.', 'mk_framework' ),
					),
			
					array(
						"heading" => __("Type", 'mk_framework'),
						"description" => __("Designates the type of slider rendered.", 'mk_framework'),
						"param_name" => "slider_type",
						"value" => array(
							__("Hero", 'mk_framework') => "hero",
							__("Post Content", 'mk_framework') => "post"
			
						),
						"type" => "dropdown"
					),
			
					array(
						"heading" => __("Controls", 'mk_framework'),
						"description" => __("Designates the type of controls rendered.", 'mk_framework'),
						"param_name" => "controls_type",
						"value" => array(
							__("Thumbnail", 'mk_framework') => "thumbnail",
							__("Arrows", 'mk_framework') => "arrow",
							__("Bullets", 'mk_framework') => "bullet",
						),
						"type" => "dropdown"
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

					/*array(
						"type" => "toggle",
						"heading" => __("Enable Excerpt", "mk_framework"),
						"param_name" => "enable_excerpt",
						"value" => "false",
						"description" => __("", "mk_framework"),
					),*/

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
						"heading" => __("Overlay classnames", "mk_framework"),
						"param_name" => "overlay_class",
						"value" => "",
						"description" => __("Add classes to the slider overlay; useful for shaders or other styling effects.", "mk_framework")
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
					'slider_type' => 'hero',
					'controls_type' => 'thumbnail',
                    'categories' => '',
                    'num_posts' => '3',
					'order' => '',
					'interval' => '7000',
					'speed' => '1000',
					'overlay_class' => '',
					'el_class' => ''
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
		
		$slider_classes = array(
			'vulcan-post-slider'
		);
		
		if ( 'hero' === $slider_type )
		{
			$slider_classes[] = 'slider-type-hero';
		}
		else if ( 'post' === $slider_type )
		{
			$slider_classes[] = 'slider-type-post';
		}
		
		$slider_classes[] = $controls_type . '_controls';
		
		$slider_classes = implode( ' ', $slider_classes );
		
		$slider_meta_data  = new class( $slider_type, $controls_type, $interval, $speed, $overlay_class )
		{
			public $type;
			public $controls;
			public $interval;
			public $speed;
			public $overlay;
			public function __construct( $slider_type, $controls_type, $interval, $speed, $overlay_class )
			{
				$this->type = $slider_type;
				$this->controls = $controls_type;
				$this->interval = $interval;
				$this->speed = $speed;
				$this->overlay = $overlay_class;
			}
		};
		
		$html = '<div id="Vulcan_Post_Slider" class="' . $slider_classes . '" data-meta=\'' . json_encode($slider_meta_data) . '\' >';
		
		$html .= '<div class="vulcan-slides-wrapper">';
		
		$my_query = new WP_Query( $query_args );
		
		if ( 'arrow' === $controls_type )
		{
			$controls[] = '<div class="arrow left"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="70" height="70" viewBox="0 0 70 70"><image id="left_arrow" data-name="left arrow" width="70" height="70" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAABI1BMVEUAAAAaM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1odNV1KXX05T3IbMVYbL1Bea4Lj5ur////+/v5ufZcbMFMfMVGSm6rT2N8aMlkbMFEsPlvBxs/y8/UaMlccL1BJWHIbMVUcL09xfZH4+PkbMFIhNFOhqbY0RWLN0dgbMldUYnrr7O8bMVSBi538/f0mOFeyuMMaMlg8TGjY2+FhboQeMVCQmam/xM1HVnDi5Ohve4/3+PkdMFCbpLKMlqYaM1krPVv6+vtwfJA9TWnZ3OGiqbcAAABxK8rDAAAAInRSTlMAAzdpkLfa6PQWbLTxBFW1+wZl3AFb4ibBavcMqhvPJOPkKkwMdQAAAAFiS0dEAIgFHUgAAAAJcEhZcwAALiMAAC4jAXilP3YAAAAHdElNRQfhDAgKNDJv4oDIAAACKElEQVRYw62Y+XcSQQzHh4XlWu77bEuocqTiUQXLlnq2HlR7aqtV+///F2XBKrBzvpfvj9m3nzeTyWSSMCZUwAqG7HAkChCNhO1Q0AowY8XiTgLWlHDiMRNGMpXOAFeZdCqpCcnm8iBRPpfVgBSKJVCoVCyoKOWKCuKpUpYvpVrToQDUqpIF1Rt6EE+NuojS3NCnAGw0+ZTNLRMKwNYml9IyowC0OJym4Vrm6/Htq27kl3/+WfNzweCMltVYPfeq+o/29oOHfmt1JXaVUdfp9vq488hnry3Fc0F1A9qDx09wpqf+T5X/2yoqKM92n7/wKDjkfCzeU7KKOz16uddHIaZ0nzdycsrY3Z+gGAO5BSUpzVLtg1evEWWY/CIfpqRu6b55i3IMpOaYtITyzn1/iCpM2qPEMmLKkfvhIyoxGe+9iIvdsvvpM6IaA/EZxhFRpt3jL6iFcWZvY0JA6bhfh6iHSQSYJdjQ4OQUURMDFgty7bPwP0N9TJCFeOZR9/wCDTAhZnOsY/fyG5pgbBb2Gw++XyEaYcIs4rMN3Gs0xERYdN30w/15Y4qJMp9p6vbQFANUGKJNEbmY6MD54fdrYoSxqS4D0dW0+B9MEwVR2pIl0d9/9DAOXUonemCInjuqx1dZCtzKMX9LAaLChKpMIiraqEpIooKWqrymKvaJWg+qRoiqLaNqEqlaVqoGmqqdZ0TDBUY16mBEgxdPJGOguSiGUguZjMjuAOEBJxJb4/x8AAAAAElFTkSuQmCC"/></svg></div>';
			$controls[] = '<div class="arrow right"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="70" height="70" viewBox="0 0 70 70"><image id="right_arrow" data-name="right arrow" width="70" height="70" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAABI1BMVEUAAAAaM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1saM1s5T3JKXX0dNV0aM1pufZf+/v7////j5upea4IbL1AbMVbT2N+Sm6ofMVEbMFPy8/XBxs8sPlsbMFEaMllJWHIcL1AaMlf4+PlxfZEcL08bMVWhqbYhNFMbMFLN0dg0RWLr7O9UYnobMlf8/f2Bi50bMVSyuMMmOFfY2+E8TGgaMlhhboSQmakeMVC/xM3i5OhHVnD3+Plve4+bpLIdMFCMlqb6+vsrPVsaM1lwfJDZ3OE9TWmiqbcAAACPsi+PAAAAInRSTlMAAzdpkLfa6PQWbLTxBFW1+wZl3AFb4ibBavcMqhvPJOPkKkwMdQAAAAFiS0dEAIgFHUgAAAAJcEhZcwAALiMAAC4jAXilP3YAAAAHdElNRQfhDAgKNDJv4oDIAAACFUlEQVRYw62YZ1MCQQyGl6P3jlSBWIPYsOup2Hvv/f//CzkYFbnbNpP36+08k8sm2SSMceUy3B6vzx8ACPh9Xo/bcDFtBUPhCAwoEg4FdRjRWDwBjkrEY1FFSDKVBoHSqaQCJJPNgUS5bEZGGcrLIJbyQ2JTCkUVCkCxIDCoVFaDWCqXeJTKsDoFYLjiTKnWdCgAtaojpa5HAag7cCqatnTtsf1XScsvv/4Z8HNG4476Vf5/7wX7iZHRsXE5p/Avdu1RNzGJjanmtAxT7IvnjEMGzGBHs3PzMovyf7+VdfjcsjC4sLi0LOFkfyjJHBeDjZXVNTEm91M3UsDH4PqGuSnmpHqUaFqEQdzabgsdlO7VwxiIMbiz2xQ6KNbFxGUY3Ns3DwSYuEUJJqQYPDwyj/mYhPVehECOQTw5XeI7KNTBhJUweHbevOBhwp23MaKGwdalyUuNiIsZoIhBvLrmpYbB3OoYvOGlhpt5NDB4e9d0TA0P8+pg8P7BMTW8zKeFQXx8atsP+5hfE4PP5rztsJ8FdDEvr+bb4OEAA10MTpn2OKTCEP0UkYuJLlwv/NbfOeFHlAxEqWmoY0SFgqhsqRbRj09hEaUq6UQPDNFzR/X4ylqBL7VWgKgxoWqTiJo2qhaSqKGlaq+pmn2i0YNqEKIay6iGRKqRlWqAphrnGdFygVGtOhjR4sUSyRqoK4qlVE86K7Jv6jYim5TgqaUAAAAASUVORK5CYII="/></svg></div>';
		}

		$i = 0;
		while ( $my_query->have_posts() && $i < $num_posts ) {
			$my_query->the_post();
			$post_title = the_title( '', '', false );
			$post_id = $my_query->post->ID;

			$content = $my_query->post->post_content;
			$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => 'slide-thumbnail' ) );
			$post_image = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'slide-fullimage' ) );
			
			if ( 'thumbnail' === $controls_type )
			{
				$thumbControl = '<button class="slide-control" data-slide-index="' . $i . '"><div class="thumbnail-transform-wrapper">' . $post_thumbnail . '</div></button>';
				$controls[] = $thumbControl;
			}
			
			if ( 'bullet' === $controls_type )
			{
				$thumbControl = '<span class="slide-control-bullet" data-slide-index="' . $i . '"></span>';
				$controls[] = $thumbControl;
			}
			
			$slide_title = null;
			$image_shader = null;
			$inner_slide = null;
			if ( 'hero' === $slider_type )
			{
				$slide_title = '<h2 class="slide-title">' . $post_title . '</h2>';
				$image_shader = '<div class="image-shader"></div>';
				$template = '<div class="slide-details">' .
								'<div class="slide-postlink"><a href="' .
								get_permalink( $my_query->post->ID ) .
								'">FIND OUT MORE</a></div>' .
							'</div>';
				$inner_slide = $template;
			}
			else if ( 'post' === $slider_type )
			{
				$inner_slide = do_shortcode( $content );
			}
			
			$html .= 
				'<figure class="slide" data-slide-index="' . $i . '">' .
					'<div class="image-wrapper">' .
						$post_image .
						$image_shader .
					'</div>' .
				 	'<figcaption class="slide-content">' .
						'<div class="slide-flex-wrapper">' .
							$slide_title .
							$inner_slide .
						'</div>' .
					'</figure>' .
				'</figure>';
			$i++;
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