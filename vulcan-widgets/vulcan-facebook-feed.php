<?php

class Vulcan_widget_upcoming_event extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'vulcan_widget_upcoming_event', // Base ID
			esc_html__( 'Vulcan Upcoming Event', 'text_domain' ), // Name
			array( 'description' => esc_html__( 'Displays the nearest upcoming event.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		$num_cards = 1;

		$events = tribe_get_events(
			array(
				'posts_per_page' => 1,
			)
		);
		
		$html = '';
		
		// The result set may be empty
		if ( ! empty( $events ) )
		{
			$i = 0;
			foreach ( $events as $post ) {
				
				$url = get_page_link( $post->ID );
			
				$post_image = get_the_post_thumbnail( $post->ID, 'full', array( 'class' => 'event-fullimage' ) );
				
				if ( $i < $num_cards )
				{
					$card_class = 'vulcan-event-card';
					if ( 0 === $i )
					{
						$html .= '<div class="vulcan-cards-wrap">';
					}
					$html .=
					'<figure class="vulcan-event ' . $card_class . '">' .
						'<div class="event-transform-wrapper">' .
							'<div class="aspect-ratio-wrap">' . $post_image . '</div>' .
							'<div class="event-details">' .
								'<div><h2 class="event-title">' . $post->post_title . '</h2></div>' .
								'<div><p class="event-content">' . $post->post_content . '</p></div>' .
								'<div class="vc_btn3-container  difThemeButton vc_btn3-center">' .
								'<button class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-round vc_btn3-style-outline vc_btn3-block vc_btn3-color-primary">' .
									'<a href="' . $url . '">View Event</a>' .
								'</button></div>' .
							'</div>' .
						'</div>' .
					'</figure>';

					if ( $num_cards - 1 === $i )
					{
						$html .= '</div>';
					}
				}
				
				$i++;
				
			}
		}
		else
		{
			$html .= 'There are no events.';
		}
         
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo $html;
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<!-- Title -->
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}