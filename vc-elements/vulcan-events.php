<?php

/*
* Vulcan Events.
* Your events, your way.
*/
class vcVulcanEvents extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanEvents_mapping' ) );
        add_shortcode( 'vc_vulcanEvents', array( $this, 'vc_vulcanEvents_html' ) );
    }
    
    public function vc_vulcanEvents_mapping()
	{
         
        // Proceed if Visual Composer is installed and activated.
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            return;
        }

        // Proceed if The Events Calender is installed and activated.
        if ( ! class_exists( 'Tribe__Events__Main' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map(
            array(
                'name' => __('Vulcan Events', 'text-domain'),
                'base' => 'vc_vulcanEvents',
                'description' => __('Your events, your way.', 'text-domain'),
                'category' => __('Vulcan', 'text-domain'),
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(
			
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Section Title', 'mk_framework' ),
						'param_name'  => 'heading',
						'description' => __( '', 'mk_framework' ),
                    ),
			
					array(
						"type" => "toggle",
						"heading" => __("Enable Minimal View", "mk_framework"),
						"param_name" => "enable_minimal",
						"value" => "false",
						"description" => __("", "mk_framework"),
                    ),
                    
                    array(
						"type" => "toggle",
						"heading" => __("Enable Event Cards", "mk_framework"),
						"param_name" => "enable_cards",
						"value" => "false",
						"description" => __("", "mk_framework"),
						"dependency" => array(
							'element' => "enable_minimal",
							'value' => array(
								'false'
							)
						)
                    ),

                    array(
						"type" => "range",
						"heading" => __("Number of cards", "mk_framework") ,
						"param_name" => "num_cards",
						"value" => "2",
						"min" => "1",
						"max" => "6",
						"step" => "1",
						"unit" => "cards",
						"description" => __("", "mk_framework"),
						"dependency" => array(
							'element' => "enable_cards",
							'value' => array(
								'true'
							)
						)
					),

                    array(
						"type" => "range",
						"heading" => __("Total number of events", "mk_framework") ,
						"param_name" => "num_events",
						"value" => "8",
						"min" => "-1",
						"max" => "24",
						"step" => "1",
						"unit" => "events",
						"description" => __("", "mk_framework"),
                    ),
                    
                    array(
						"type" => "toggle",
						"heading" => __("Enable View All Button", "mk_framework"),
						"param_name" => "enable_view_all",
						"value" => "false",
						"description" => __("", "mk_framework"),
						"dependency" => array(
							'element' => "enable_minimal",
							'value' => array(
								'false'
							)
						)
					),
                    
                    array(
						'type'        => 'textfield',
						'heading'     => __( 'Categories', 'mk_framework' ),
						'param_name'  => 'category',
						'description' => __( 'Enter specific Categories to be used.', 'mk_framework' ),
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
    
    public function vc_vulcanEvents_html( $atts )
	{
        extract(
            shortcode_atts(
                array(
					'heading' => '',
					'enable_minimal' => false,
					'enable_cards' => false,
                    'num_cards'   => 2,
                    'num_events' => 8,
					'enable_view_all' => false,
					'category' => '',
					'el_class' => '',
                ), 
                $atts
            )
        );

        $events = tribe_get_events(
			array(
				'posts_per_page' => $num_events,
				'eventDisplay' => 'upcoming',
			)
		);
		
		if ( $enable_minimal )
		{
			$minimal_view_class .= ' vulcan-minimal-list';
		}
        
		$html = '<section class="vulcan-events' . $minimal_view_class . '" data-num_cards="' . $num_cards . '">';
		$html .= '<h1 class="vulcan-title">' . $heading . '</h1>';
		
		// The result set may be empty
		if ( ! empty( $events ) )
		{
			$i = 0;
			foreach ( $events as $post ) {
				
				$url = get_page_link($post->ID);
				
				// Filter for categories or just make category list available
				$terms = wp_get_post_terms( $post->ID, Tribe__Events__Main::TAXONOMY );
				$categories = array();
				if ( ! empty( $terms ) )
				{
					foreach ( $terms as $term )
					{
						if ( $term->taxonomy === 'tribe_events_cat' )
						{
							$categories[] = $term->name;
						}
					}
					if ( '' !== $category && ! in_array( $category, $categories ) )
					{
						continue;
					}
				}
				else if ( '' !== $category )
				{
					continue;
				}

				// Expose categories as html
				$categories_html = '';
				foreach ( $categories as $cat )
				{
					$categories_html .= '<span class="event-category">' . $cat . '</span>';
				}
			
				$post_image = get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'event-fullimage' ) );
				$post_start_day = self::get_month_day( $post->EventStartDate );
				$post_end_day = self::get_month_day( $post->EventEndDate );
				$post_start_time = self::get_hour_minute( $post->EventStartDate );
				$post_end_time = self::get_hour_minute( $post->EventEndDate );
				
				
				$card_class = '';
				
				if ( false === (bool)$enable_minimal && true === (bool)$enable_cards && $i < $num_cards )
				{
					$card_class = 'vulcan-event-card';
					if ( 0 === $i )
					{
						$html .= '<div class="vulcan-cards-wrap">';
					}
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">' .
						'<a href="' . $url . '">' .
							'<div class="event-transform-wrapper">' .
								'<div class="aspect-ratio-wrap">' . $post_image . '</div>' .
								'<div class="event-details">'.
									$post_start_day .
									'<div class="event-category">' .$categories_html . '</div>' .
									'<h2 class="event-title">' . $post->post_title . '</h2>' .
									'<p class="event-content">' . wp_strip_all_tags( get_the_excerpt( $post ), true ) . '</p>' .
								'</div>' .
							'</div>' .
						'</a>' .
					'</figure>';

					if ( $num_cards - 1 === $i )
					{
						$html .= '</div>';
					}
				}
				else if ( false === (bool)$enable_minimal )
				{
					$card_class = 'vulcan-event-listitem';
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">' .
						'<a href="' . $url . '">' .
							'<div class="event-transform-wrapper">' .
								$post_start_day .
								'<div class="event-details">' .
									'<h2 class="event-title">' . $post->post_title . '</h2>' .
									'<div class="event-categories">' .$categories_html . '</div>' .
									'<div class="event-timeframe">' . $post_start_time . ' - ' . $post_end_time . '</div>' .
								'</div>' .
							'</div>' .
						'</a>' .
					'</figure>';
				}
				else
				{
					$card_class = 'vulcan-event-listitem';
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">' .
						'<a href="' . $url . '">' .
							'<div class="event-transform-wrapper">' .
								'<h2 class="event-title">' . $post->post_title . '</h2>' .
								'<div class="event-details">' .
									$post_start_day .
									'<div class="event-timeframe">' . $post_start_time . ' - ' . $post_end_time . '</div>' .
								'</div>' .
							'</div>' .
						'</a>' .
					'</figure>';
				}
				
				$i++;
				
			}
		}
		else
		{
			$html .= 'There are no events.';
		}
		
		if ( true == $enable_view_all )
		{
			$html .= '<div class="vulcan-view-all-events">
						<a href="' . get_site_url() . '/events/">View all events</a>
					</div>';
		}
		
        $html .= '</section>';
         
        return $html;
         
    }
	
	private function get_month_day( $date_string )
	{
		$date = date_parse( $date_string );
		$m = null;
		switch ( (int)$date['month'] )
		{
			case 1:
				$m = 'JAN';
				break;
			case 2:
				$m = 'FEB';
				break;
			case 3:
				$m = 'MAR';
				break;
			case 4:
				$m = 'APR';
				break;
			case 5:
				$m = 'MAY';
				break;
			case 6:
				$m = 'JUN';
				break;
			case 7:
				$m = 'JUL';
				break;
			case 8:
				$m = 'AUG';
				break;
			case 9:
				$m = 'SEP';
				break;
			case 10:
				$m = 'OCT';
				break;
			case 11:
				$m = 'NOV';
				break;
			case 12:
				$m = 'DEC';
				break;
			default:
				break;
		}
		$date_html =
		'<div class="event-date">
			<span class="month">' . $m . '</span>
			<span class="day">' . $date['day'] . '</span>
		</div>';
		return $date_html;
	}
	
	private function get_hour_minute( $date_string )
	{
		return date( 'g:i a', strtotime( $date_string ) );
	}
     
}

new vcVulcanEvents();



















