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
						"type" => "toggle",
						"heading" => __("Enable Event Cards", "mk_framework"),
						"param_name" => "enable_cards",
						"value" => "false",
						"description" => __("", "mk_framework"),
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
					),

                    array(
						"type" => "range",
						"heading" => __("Total nubmer of events", "mk_framework") ,
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
			)
		);
        
		$html = '<section class="vulcan-events">';
		
		// The result set may be empty
		if ( ! empty( $events ) )
		{
			$i = 0;
			foreach ( $events as $event ) {
				
				var_dump( get_the_category( $event->ID ) );
			
				$event_image = get_the_post_thumbnail( $event->ID, 'full', array( 'class' => 'event-fullimage' ) );
				$event_start_day = self::get_month_day( $event->EventStartDate );
				$event_end_day = self::get_month_day( $event->EventEndDate );
				$event_start_time = self::get_hour_minute( $event->EventStartDate );
				$event_end_time = self::get_hour_minute( $event->EventEndDate );
				var_dump( $event );
				
				
				$card_class = '';
				
				if ( true === (bool)$enable_cards && $i < $num_cards )
				{
					$card_class = 'vulcan-event-card';
					if ( 0 === $i )
					{
						$html .= '<div class="vulcan-cards-wrap">';
					}
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">
						<div class="event-transform-wrapper">' .
							$event_image .
							$event_start_day .
							'<h1 class="event-title">' . $event->post_title . '</h1>
							<p class="event-content">' . $event->post_content . '</p>
						</div>
					</figure>';

					if ( $num_cards - 1 === $i )
					{
						$html .= '</div>';
					}
				}
				else
				{
					$card_class = 'vulcan-event-listitem';
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">
						<div class="event-transform-wrapper">' .
							$event_start_day .
							'<h1 class="event-title">' . $event->post_title . '</h1>
							<p class="event-category">' /*. $event->post_content*/ . '</p>
							<p class="event-timeframe">' . $event_start_time . ' - ' . $event_end_time . '</p>
						</div>
					</figure>';
				}
				
				$i++;
				
			}
		}
		else
		{
			$html .= 'There are no events.';
		}
		
		if ( true === $enable_view_all )
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
		$date = date_parse( $date_string );
		
		$meridiem = '';
		$hour = $date['hour'];
		if ( $hour >= 12 )
		{
			$meridiem = 'PM';
		}
		else
		{
			$meridiem = 'AM';
		}
		if ( $hour > 12 )
		{
			$hour -= 12;
		}
		
		$date_html =
		'<div class="event-time">
			<span class="hour">' . $hour . '</span>
			<span class="minute">' . $date['minute'] . '</span>
			<span class="meridiem">' . $meridiem . '</span>
		</div>';
	}
     
}

new vcVulcanEvents();



















