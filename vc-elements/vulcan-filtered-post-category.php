<?php

/*
* Vulcan Filtered Post Category
* Outputs a category, or categories, of posts and enables the results to be filtered by tags.
*/
class vcVulcanFilteredCategory extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanFilteredCategory_mapping' ) );
        add_shortcode( 'vc_vulcanFilteredCategory', array( $this, 'vc_vulcanFilteredCategory_html' ) );
		add_action( 'wp_enqueue_scripts', 'vulcan_filtered_category_js', 10 );
		function vulcan_filtered_category_js() {
			$path = get_stylesheet_directory_uri() . '/assets/js/vulcan-filtered-category-controls.js';
			wp_enqueue_script( 'vulcan-filtered-category-js', $path, array(), vulcan_get_file_version($path), true);
		}
    }
    
    public function vc_vulcanFilteredCategory_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Filtered Category List', 'text-domain'),
                'base' => 'vc_vulcanFilteredCategory',
                'description' => __('Outputs a category, or categories, of posts and enables the results to be filtered by tags.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(
			
					array(
						'type' => 'textfield',
						'heading' => __('Section Title', 'mk_framework'),
						'param_name' => 'section_title',
						'value' => '',
						'description' => __('', 'mk_framework')
					),
			
					array(
						'type' => 'textfield',
						'heading' => __('Category Name(s)', 'mk_framework'),
						'param_name' => 'categories',
						'value' => '',
						'description' => __('', 'mk_framework')
					),
			
					array(
						'type' => 'textfield',
						'heading' => __('Tag(s)', 'mk_framework'),
						'param_name' => 'tags',
						'value' => '',
						'description' => __('', 'mk_framework')
					),
			
					array(
						'type' => 'toggle',
						'heading' => __('Enable Excerpt', 'mk_framework'),
						'param_name' => 'enable_excerpt',
						'value' => false,
						'description' => __('', 'mk_framework'),
					),
			
					array(
						'type' => 'toggle',
						'heading' => __('Enable Link to Post', 'mk_framework'),
						'param_name' => 'enable_link_to_post',
						'value' => false,
						'description' => __('Adds a button that links to the source post.', 'mk_framework'),
					),

					array(
						'type' => 'textfield',
						'heading' => __('Extra class name', 'mk_framework'),
						'param_name' => 'el_class',
						'value' => '',
						'description' => __('If you wish to style particular content Filtered Category differently, then use this field to add a class name and then refer to it in Custom CSS Shortcode or Masterkey Custom CSS option.', 'mk_framework')
					)
                        
                )
            )
        );                                
        
    }
    
    public function vc_vulcanFilteredCategory_html( $atts )
	{
        /*
		* Setup Attributes
		*/
		extract(
            shortcode_atts(
                array(
					'section_title' => '',
					'categories' => '',
					'tags' => '',
					'enable_excerpt' => false,
					'enable_link_to_post' => false,
					'el_class' => '',
                ), 
                $atts
            )
        );
		
		$enable_excerpt = filter_var( $enable_excerpt, FILTER_VALIDATE_BOOLEAN );
		$enable_link_to_post = filter_var( $enable_link_to_post, FILTER_VALIDATE_BOOLEAN );
		
		/*
		* Setup the query.
		*/
		$query_args = array(
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
			'nopaging' => true,
			'posts_per_page' => -1
		);

		if ( '' !== $categories )
		{
			$categories = explode( ',', $categories );
			$gc = array();
			foreach ( $categories as $category ) {
				$gc[] = toLowerAndHypenate( $category );
			}
			$gc = implode( ',', $gc );
			$query_args['category_name'] = $gc;
		}
		
		if ( '' !== $tags )
		{
			$tags = explode( ',', $tags );
			$gc = array();
			foreach ( $tags as $tag ) {
				$gc[] = toLowerAndHypenate( $tag );
			}
			$gc = implode( ',', $gc );
			$query_args['tag'] = $gc;
		}
		
		/*
		* Classes to be added to the component root.
		*/
		$container_classes = array(
			'vulcan-filtered-post-category',
			'vulcan-container'
		);
		
		if ( true === $enable_excerpt )
		{
			$container_classes[] = 'force-one-column';
		}
		
		/*
		* Initialize variables.
		*/
		$the_tags = array();
		
		$the_filter = null;
		
		$the_items = array();
		
		/*
		* Do query.
		*/
		$the_query = new WP_Query( $query_args );
		
		/*
		* Loop query.
		*/
		while ( $the_query->have_posts() ) {
			/*
			* Setup the Post.
			*/
			$the_query->the_post();
			
			/*
			* Build the tag filter data.
			*/
			$the_terms = wp_get_post_tags( $the_query->post->ID );
			if ( empty( $the_terms ) )
			{
				$the_tags[] = 'uncategorized';
			}
			else
			{
				foreach ( $the_terms as $term )
				{
					$the_tags[] = $term->name;
				}
			}
			
			/*
			* Build the item data.
			*/
			$the_items[] = new VulcanFilteredCategory_CategoryItem(
				$the_query->post,
				get_post_meta( $the_query->post->ID ),
				wp_get_post_tags( $the_query->post->ID ),
				(bool)$enable_excerpt,
				(bool)$enable_link_to_post
			);
		}
		
		/*
		* Build the filter.
		*/
		$the_tags = array_unique( $the_tags );
		sort( $the_tags );
		$the_filter = new VulcanFilteredCategory_FilterControls( $the_tags );

		/*
		* Build the component.
		*/
		$html = '<section class="' . implode( ' ', $container_classes ) . '">';
			$html .= '<div class="vc_separator"><span class="vc_sep_holder vc_sep_holder_l"><span class="vc_sep_line"></span></span><h1>';
			$html .= $section_title;
			$html .= '</h1><span class="vc_sep_holder vc_sep_holder_r"><span class="vc_sep_line"></span></span></div>';
			/*
			* Render the controls.
			*/
			$html .= $the_filter->render();
			$html .= '<div class="category-posts-wrapper">';
				/*
				* Render the items.
				*/
				foreach ( $the_items as $the_item )
				{
					$html .= $the_item->render();
				}
			$html .= '</div>';
		$html .= '</section>';
		
		wp_reset_query();
        
		/*
		* Render the component.
		*/
        return $html;
		
	}
     
}
new vcVulcanFilteredCategory();

class VulcanFilteredCategory_FilterControls
{
	private $tags = null;
	public function __construct( $raw_tags )
	{
		foreach ( $raw_tags as $tag )
		{
			$this->tags[] = 
			'<div class="filtered-post-category-control">
				<div class="slidebox">
					<input type="checkbox" id="' . $tag . '" name="' . $tag . '" value="' . $tag . '" checked>
					<label for="' . $tag . '"></label>
				</div>
				<span class="control-label">' . $tag . '</span>
			</div>';
		}
		
	}
	
	public function render()
	{
		$html = '<form class="filtered-post-category-controls">';
			$html .= 
			'<header>
				<strong>Category Filters</strong>
				<span>Toggle filters to reveal or hide items belonging to that tag.</span>
			</header>';
			$html .= implode( '', $this->tags );
		$html .= '</form>';
		return $html;
	}
}

class VulcanFilteredCategory_CategoryItem
{
	private $location_svg = '<svg class="mk-svg-icon" data-name="mk-icon-home" data-cacheid="icon-5a32b54f9424b" style=" height:16px; width: 14.857142857143px; " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1664 1792"><path d="M1408 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"></path></svg>';
		
	private $website_svg = '<svg class="mk-svg-icon" data-name="mk-icon-globe" data-cacheid="icon-5a32b54f94367" style=" height:16px; width: 13.714285714286px; " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1536 1792"><path d="M768 128q209 0 385.5 103t279.5 279.5 103 385.5-103 385.5-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103zm274 521q-2 1-9.5 9.5t-13.5 9.5q2 0 4.5-5t5-11 3.5-7q6-7 22-15 14-6 52-12 34-8 51 11-2-2 9.5-13t14.5-12q3-2 15-4.5t15-7.5l2-22q-12 1-17.5-7t-6.5-21q0 2-6 8 0-7-4.5-8t-11.5 1-9 1q-10-3-15-7.5t-8-16.5-4-15q-2-5-9.5-10.5t-9.5-10.5q-1-2-2.5-5.5t-3-6.5-4-5.5-5.5-2.5-7 5-7.5 10-4.5 5q-3-2-6-1.5t-4.5 1-4.5 3-5 3.5q-3 2-8.5 3t-8.5 2q15-5-1-11-10-4-16-3 9-4 7.5-12t-8.5-14h5q-1-4-8.5-8.5t-17.5-8.5-13-6q-8-5-34-9.5t-33-.5q-5 6-4.5 10.5t4 14 3.5 12.5q1 6-5.5 13t-6.5 12q0 7 14 15.5t10 21.5q-3 8-16 16t-16 12q-5 8-1.5 18.5t10.5 16.5q2 2 1.5 4t-3.5 4.5-5.5 4-6.5 3.5l-3 2q-11 5-20.5-6t-13.5-26q-7-25-16-30-23-8-29 1-5-13-41-26-25-9-58-4 6-1 0-15-7-15-19-12 3-6 4-17.5t1-13.5q3-13 12-23 1-1 7-8.5t9.5-13.5.5-6q35 4 50-11 5-5 11.5-17t10.5-17q9-6 14-5.5t14.5 5.5 14.5 5q14 1 15.5-11t-7.5-20q12 1 3-17-5-7-8-9-12-4-27 5-8 4 2 8-1-1-9.5 10.5t-16.5 17.5-16-5q-1-1-5.5-13.5t-9.5-13.5q-8 0-16 15 3-8-11-15t-24-8q19-12-8-27-7-4-20.5-5t-19.5 4q-5 7-5.5 11.5t5 8 10.5 5.5 11.5 4 8.5 3q14 10 8 14-2 1-8.5 3.5t-11.5 4.5-6 4q-3 4 0 14t-2 14q-5-5-9-17.5t-7-16.5q7 9-25 6l-10-1q-4 0-16 2t-20.5 1-13.5-8q-4-8 0-20 1-4 4-2-4-3-11-9.5t-10-8.5q-46 15-94 41 6 1 12-1 5-2 13-6.5t10-5.5q34-14 42-7l5-5q14 16 20 25-7-4-30-1-20 6-22 12 7 12 5 18-4-3-11.5-10t-14.5-11-15-5q-16 0-22 1-146 80-235 222 7 7 12 8 4 1 5 9t2.5 11 11.5-3q9 8 3 19 1-1 44 27 19 17 21 21 3 11-10 18-1-2-9-9t-9-4q-3 5 .5 18.5t10.5 12.5q-7 0-9.5 16t-2.5 35.5-1 23.5l2 1q-3 12 5.5 34.5t21.5 19.5q-13 3 20 43 6 8 8 9 3 2 12 7.5t15 10 10 10.5q4 5 10 22.5t14 23.5q-2 6 9.5 20t10.5 23q-1 0-2.5 1t-2.5 1q3 7 15.5 14t15.5 13q1 3 2 10t3 11 8 2q2-20-24-62-15-25-17-29-3-5-5.5-15.5t-4.5-14.5q2 0 6 1.5t8.5 3.5 7.5 4 2 3q-3 7 2 17.5t12 18.5 17 19 12 13q6 6 14 19.5t0 13.5q9 0 20 10t17 20q5 8 8 26t5 24q2 7 8.5 13.5t12.5 9.5l16 8 13 7q5 2 18.5 10.5t21.5 11.5q10 4 16 4t14.5-2.5 13.5-3.5q15-2 29 15t21 21q36 19 55 11-2 1 .5 7.5t8 15.5 9 14.5 5.5 8.5q5 6 18 15t18 15q6-4 7-9-3 8 7 20t18 10q14-3 14-32-31 15-49-18 0-1-2.5-5.5t-4-8.5-2.5-8.5 0-7.5 5-3q9 0 10-3.5t-2-12.5-4-13q-1-8-11-20t-12-15q-5 9-16 8t-16-9q0 1-1.5 5.5t-1.5 6.5q-13 0-15-1 1-3 2.5-17.5t3.5-22.5q1-4 5.5-12t7.5-14.5 4-12.5-4.5-9.5-17.5-2.5q-19 1-26 20-1 3-3 10.5t-5 11.5-9 7q-7 3-24 2t-24-5q-13-8-22.5-29t-9.5-37q0-10 2.5-26.5t3-25-5.5-24.5q3-2 9-9.5t10-10.5q2-1 4.5-1.5t4.5 0 4-1.5 3-6q-1-1-4-3-3-3-4-3 7 3 28.5-1.5t27.5 1.5q15 11 22-2 0-1-2.5-9.5t-.5-13.5q5 27 29 9 3 3 15.5 5t17.5 5q3 2 7 5.5t5.5 4.5 5-.5 8.5-6.5q10 14 12 24 11 40 19 44 7 3 11 2t4.5-9.5 0-14-1.5-12.5l-1-8v-18l-1-8q-15-3-18.5-12t1.5-18.5 15-18.5q1-1 8-3.5t15.5-6.5 12.5-8q21-19 15-35 7 0 11-9-1 0-5-3t-7.5-5-4.5-2q9-5 2-16 5-3 7.5-11t7.5-10q9 12 21 2 7-8 1-16 5-7 20.5-10.5t18.5-9.5q7 2 8-2t1-12 3-12q4-5 15-9t13-5l17-11q3-4 0-4 18 2 31-11 10-11-6-20 3-6-3-9.5t-15-5.5q3-1 11.5-.5t10.5-1.5q15-10-7-16-17-5-43 12zm-163 877q206-36 351-189-3-3-12.5-4.5t-12.5-3.5q-18-7-24-8 1-7-2.5-13t-8-9-12.5-8-11-7q-2-2-7-6t-7-5.5-7.5-4.5-8.5-2-10 1l-3 1q-3 1-5.5 2.5t-5.5 3-4 3 0 2.5q-21-17-36-22-5-1-11-5.5t-10.5-7-10-1.5-11.5 7q-5 5-6 15t-2 13q-7-5 0-17.5t2-18.5q-3-6-10.5-4.5t-12 4.5-11.5 8.5-9 6.5-8.5 5.5-8.5 7.5q-3 4-6 12t-5 11q-2-4-11.5-6.5t-9.5-5.5q2 10 4 35t5 38q7 31-12 48-27 25-29 40-4 22 12 26 0 7-8 20.5t-7 21.5q0 6 2 16z"></path></svg>';
	
	private $meta = array(
		'location' => null,
		'link' => null
	);
	
	private $tags = array();
	
	private $title = null;
	
	public function __construct( $raw_post, $raw_meta, $raw_terms, $enable_excerpt, $enable_link_to_post )
	{
		$this->id = $raw_post->ID;
		
		$this->excerpt = $enable_excerpt;
		
		$this->enable_link_to_post = $enable_link_to_post;
		
		// setup title
		$this->title = $raw_post->post_title;
		
		// setup meta
		if ( $raw_meta['location'] )
		{
			$this->meta['location'] = $raw_meta['location'][0];
		}
		if ( $raw_meta['link'] )
		{
			$this->meta['link'] = $raw_meta['link'][0];
		}
		
		// setup the tags
		if ( empty( $raw_terms ) )
		{
			$this->tags[] = 'uncategorized';
		}
		else
		{
			foreach ( $raw_terms as $term )
			{
				$this->tags[] = $term->name;
			}
		}
		
		// setup the content
		if ( true === $this->excerpt )
		{
			$this->content = $raw_post->post_content;
		}
		
	}
	
	public function render()
	{
		$image = get_the_post_thumbnail( $this->id, 'full', array( 'class' => 'slide-fullimage' ) );
		$image_html = '<div class="aspect-ratio-wrapper">' . $image . '</div>';
		$html .= 
		'<figure class="filtered-post-category-item" data-tags=\'' . json_encode( $this->tags ) . '\'">' .
			( $image ? $image_html : '' ) .
			'<h2 class="filter-post-category-title">' . $this->title . '</h2>' .
			'<div class="filter-post-category-tags">' .
			implode( ', ', $this->tags ) .
			'</div>' .
			'<figcaption class="filter-post-category-content">' .
				'<ul>';
				if ( $this->meta['location'] )
				{
					$html .= '<li>' . $this->location_svg . ' <a href="' . 'https://www.google.com/maps/search/?api=1&query=' . urlencode( $this->meta['location'] ) . '" target="_blank">' . $this->meta['location'] . '</a></li>';
				}
				if ( $this->meta['link'] )
				{
					$html .= '<li>' . $this->website_svg . ' <a href="' . $this->meta['link'] . '" target="_blank">' . $this->meta['link'] . '</a></li>';
				}
				$html .= '</ul>';
				if ( true === $this->excerpt )
				{
					$html .= '<p>' . do_shortcode( $this->content ) . '</p>';
				}
				if ( true === $this->enable_link_to_post )
				{
					$html .= '<p><a class="filter-post-category-link-to-post" href="' . get_permalink( $this->id ) . '">View Post</a></p>';
				}
			$html .= '</figcaption>' .
		'</figure>';
		return $html;
	}
	
}

function toLowerAndHypenate( $string )
{
	$string = strtolower( $string );
	$string = trim( $string );
	$string = str_replace( ' ', '-', $string );
	return $string;
}