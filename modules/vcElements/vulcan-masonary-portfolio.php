<?php

/*
* TODO: Strip out post type and meta box and create utility classes for them.
* TODO: Create a masonary post grid.
*/

class VcYogaSlider extends WPBakeryShortcode
{
    function __construct()
	{
		$this->register_post_type();
		$this->register_meta_box();
        add_action( 'init', array( $this, 'wpb_register' ) );
        add_shortcode( 'vc_yoga_slider', array( $this, 'render' ) );
    }
	
	private function register_post_type()
	{
		register_post_type(
			'yoga_slide',
			array(
				'label' => 'Yoga Slides',
				'labels' => array(
					'name' => 'Yoga Slides',
					'singular_name' => 'Yoga Slide',
					'add_new' => 'Add New Yoga Slide',
					'add_new_item' => 'Add a new post of type Yoga Slide',
					'edit_item' => 'Edit Yoga Slide',
					'new_item' => 'New Yoga Slide',
					'view_item' => 'View Yoga Slide',
					'search_items' => 'Search Yoga Slides',
					'not_found' =>  'No Yoga Slides found',
					'not_found_in_trash' => 'No Yoga Slides currently trashed',
					'parent_item_colon' => ''
				),
				'description' => 'Yoga Slides to be used in the Yoga Slider.',
				'public' => false,
				'hierarchical' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,
				'show_in_rest' => false,
				'rest_base' => null,
				'rest_controller_class' => null,
				'menu_position' => null,
				'menu_icon' => null,
				'map_meta_cap' => true,
				'supports' => array(
					'title',
					'editor',
					//'revisions',
					'page-attributes',
					'thumbnail'
				),
				//'register_meta_box_cb' => 'yoga_slide_link_url_callback',
				'taxonomies' => array(
					'category',
					'post_tag'
				),
				'has_archive' => false,
			)
        );
	}
	private function register_meta_box()
	{
		function yoga_slide_add_link_url_metabox()
		{
			add_meta_box(
				'yoga_slide_link_url',
				'Slide Link URL',
				'yoga_slide_link_url_callback',
				'yoga_slide',
				'normal'
			);
		}
		
		function yoga_slide_link_url_callback( $post)
		{
			wp_nonce_field( basename( __FILE__ ), 'yoga_slide_link_url_nonce' );
			
			$value_Link_url = get_post_meta( $post->ID, 'yoga_slide_link_url_field', true );
			$value_new_tab_toggle = get_post_meta( $post->ID, 'yoga_slide_new_tab_toggle', true );
			
			$toggle_checked = '';
			if ( true === filter_var( $value_new_tab_toggle, FILTER_VALIDATE_BOOLEAN ) )
			{
				$toggle_checked = 'checked';
			}
			
			echo '<label for="yoga_slide_link_url_field">Link URL: </label>';
			echo '<input type="text" id="yoga_slide_link_url_field" name="yoga_slide_link_url_field" value="' . esc_textarea( $value_Link_url )  . '" class="widefat">';
			echo '<hr>';
			echo '<label for="yoga_slide_new_tab_toggle">Open in new tab? </label>';
			echo '<input type="checkbox" id="yoga_slide_new_tab_toggle" name="yoga_slide_new_tab_toggle" '. $toggle_checked .' class="widefat">';
			
		}	
		
		function yoga_slide_save_link_url( $post_id )
		{
 
			// Checks save status
			$is_autosave = wp_is_post_autosave( $post_id );
			$is_revision = wp_is_post_revision( $post_id );
			$is_valid_nonce = ( isset( $_POST[ 'yoga_slide_link_url_nonce' ] ) && wp_verify_nonce( $_POST[ 'yoga_slide_link_url_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

			// Exits script depending on save status
			if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
				return;
			}

			// Checks for input and sanitizes/saves if needed
			if( isset( $_POST[ 'yoga_slide_link_url_field' ] ) ) {
				update_post_meta( $post_id, 'yoga_slide_link_url_field', sanitize_text_field( $_POST[ 'yoga_slide_link_url_field' ] ) );
			}
			
			if( isset( $_POST[ 'yoga_slide_new_tab_toggle' ] ) ) {
				$data = sanitize_text_field( $_POST[ 'yoga_slide_new_tab_toggle' ] ) ? true : false;
				update_post_meta( $post_id, 'yoga_slide_new_tab_toggle', $data );
			}
			else
			{
				delete_post_meta( $post_id, 'yoga_slide_new_tab_toggle' );
			}

		}
		
		add_action( 'add_meta_boxes', 'yoga_slide_add_link_url_metabox' );
		
		add_action( 'save_post', 'yoga_slide_save_link_url' );
	}
    
    public function wpb_register()
	{
        if ( ! defined( 'WPB_VC_VERSION' ) ) return;
        vc_map( 
            array(
                'name' => __('Yoga Slider', 'text-domain'),
                'base' => 'vc_yoga_slider',
                'description' => __('Custom slider for the Yoga Shop.', 'text-domain'),
                'category' => __('DIF Design', 'text-domain'),
                'icon' => 'https://theyogashop.us/wp-content/uploads/2017/11/hover-copy-1.png',
                'params' => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Categories', 'mk_framework' ),
						'param_name'  => 'categories',
						'value' => '',
						'description' => __( 'Enter specific Categories to be used.', 'mk_framework' ),
                    ),
                    
					array(
						"type" => "range",
						"heading" => __("Number of posts", "mk_framework") ,
						"param_name" => "num_posts",
						"value" => "3",
						"min" => "-1",
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
    
    public function render( $atts )
	{

        extract(
            shortcode_atts(
                array(
                    'categories' => '',
                    'num_posts' => '3',
					'interval' => '7000',
					'speed' => '1000',
					'el_class' => ''
                ), 
                $atts
            )
        );
		
		$query_args = array(
            'post_status' => 'publish',
            'post_type' => 'yoga_slide',
			'orderby' => 'date',
			'order' => 'DESC'
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

        $the_query = new WP_Query( $query_args );

        
        $slides = array();

        $i = 0;
		while ( $the_query->have_posts() && $i < $num_posts ) {
			$the_query->the_post();
			$post_title = the_title( '', '', false );
			$post_id = $the_query->post->ID;
			$content = $the_query->post->post_content;
            $post_image = get_the_post_thumbnail_url( $post_id, 'full', array( 'class' => 'slide-fullimage' ) );
            $link = get_post_meta( $post_id, 'yoga_slide_link_url_field', true );
			$new_tab = ( get_post_meta( $post_id, 'yoga_slide_new_tab_toggle', true ) ) ? true : false ;

            $slide = new Class( $post_image, $post_title, $content, $link, $new_tab )
            {
                function __construct( $url, $title, $text, $target, $new_tab )
                {
                    $this->url = $url;
                    $this->title = $title;
                    $this->text = $text;
                    $this->target = $target;
					$this->new_tab = $new_tab;
                }
            };

            $slides[] = $slide;

        }
		
		wp_reset_query();


        ob_start();
        ?>
        <figure id="marketmentors-yoga-slider" class="YogaSlider" data-dhs-slides='<?php echo json_encode( $slides ); ?>'></figure>
        <?php
        return ob_get_clean();
    }
}
new VcYogaSlider();
