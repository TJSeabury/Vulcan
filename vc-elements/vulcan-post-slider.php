<?php
/*
* Vulcan Post Slider.
* Turns a category of posts into a hero slider.
*/
 
// Element Class 
class vcVulcanPostSlider extends WPBakeryShortCode
{
     
    // Element Init
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanPostSlider_mapping' ) );
        add_shortcode( 'vc_vulcanPostSlider', array( $this, 'vc_vulcanPostSlider_html' ) );
    }
     
    // Element Mapping
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
                        'type' => 'textfield',
                        'heading' => __( 'Category', 'text-domain' ),
                        'param_name' => 'category',
                        'value' => __( '', 'text-domain' ),
                        'description' => __( 'The category of posts to use.', 'text-domain' ),
                        'admin_label' => false,
                        'weight' => 0,
                    ),
			
					array(
						"type" => "range",
						"heading" => __("Number of posts", "mk_framework") ,
						"param_name" => "number_posts",
						"value" => "3",
						"min" => "1",
						"max" => "12",
						"step" => "1",
						"unit" => "slides",
						"description" => __("Number of posts.", "mk_framework"),
					),
                        
                ),
            )
        );                                
        
    }
     
     
    // Element HTML
    public function vc_vulcanPostSlider_html( $atts )
	{
         
        // Params extraction
        extract(
            shortcode_atts(
                array(
                    'category'   => '',
                    'number_posts' => '3',
                ), 
                $atts
            )
        );
		
		$query_args = array(
			'post_status' => 'publish',
		);

		// Narrow by categories
		if ( '' !== $categories )
		{
			$categories = explode( ',', $categories );
			$gc = array();
			foreach ( $categories as $grid_cat ) {
				array_push( $gc, $grid_cat );
			}
			$gc = implode( ',', $gc );
			// http://snipplr.com/view/17434/wordpress-get-category-slug/
			$query_args['category_name'] = $gc;
		}

		// Order posts
		/*if ( null !== $orderby )
		{
			$query_args['orderby'] = $orderby;
		}
		$query_args['order'] = $order;*/

		// Run query
		$my_query = new WP_Query( $query_args );

		$teasers = '';
		$i = - 1;

		while ( $my_query->have_posts() ) {
			$i ++;
			$my_query->the_post();
			$post_title = the_title( '', '', false );
			$post_id = $my_query->post->ID;
			if ( in_array( get_the_ID(), $vc_posts_grid_exclude_id ) ) {
				continue;
			}
			if ( 'teaser' === $slides_content ) {
				$content = apply_filters( 'the_excerpt', get_the_excerpt() );
			} else {
				$content = '';
			}
			$thumbnail = '';

			// Thumbnail logic
			$post_thumbnail = $p_img_large = '';

			$post_thumbnail = wpb_getImageBySize( array( 'post_id' => $post_id, 'thumb_size' => $thumb_size ) );
			$thumbnail = $post_thumbnail['thumbnail'];
			$p_img_large = $post_thumbnail['p_img_large'];

			// Link logic
			if ( 'link_no' !== $link ) {
				if ( 'link_post' === $link ) {
					$link_image_start = '<a class="link_image" href="' . get_permalink( $post_id ) . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), the_title_attribute( 'echo=0' ) ) . '">';
				} elseif ( 'link_image' === $link ) {
					$p_video = get_post_meta( $post_id, '_p_video', true );
					//
					if ( '' !== $p_video ) {
						$p_link = $p_video;
					} else {
						$p_link = $p_img_large[0];
					}
					$link_image_start = '<a class="link_image prettyphoto" href="' . $p_link . '" ' . $pretty_rel_random . ' title="' . the_title_attribute( 'echo=0' ) . '" >';
				} elseif ( 'custom_link' === $link ) {
					if ( isset( $custom_links[ $i ] ) ) {
						$slide_custom_link = $custom_links[ $i ];
					} else {
						$slide_custom_link = $custom_links[0];
					}
					$link_image_start = '<a class="link_image" href="' . $slide_custom_link . '">';
				}

				$link_image_end = '</a>';
			} else {
				$link_image_start = '';
				$link_image_end = '';
			}

			$description = '';
			if ( '' !== $slides_content && '' !== $content && ( ' wpb_flexslider flexslider_fade flexslider' === $type || ' wpb_flexslider flexslider_slide flexslider' === $type ) ) {
				$description = '<div class="flex-caption">';
				if ( $slides_title ) {
					$description .= '<h2 class="post-title">' . $link_image_start . $post_title . $link_image_end . '</h2>';
				}
				$description .= $content;
				$description .= '</div>';
			}

			$teasers .= $el_start . $link_image_start . $thumbnail . $link_image_end . $description . $el_end;
		} // endwhile loop
		wp_reset_query();
         
        // Fill $html var with data
        $html = '
        <div class="vc-infobox-wrap">
         
            <h2 class="vc-infobox-title">' . $category . '</h2>
             
            <div class="vc-infobox-text">' . $number_posts . '</div>
         
        </div>';      
         
        return $html;
         
    }
     
} // End Element Class
 
 
// Element Class Init
new vcVulcanPostSlider();