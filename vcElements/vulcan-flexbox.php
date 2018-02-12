<?php

/*
* Vulcan Flex box.
* Flex your content, bruh. Crack open a cold one wit da boiz, bruh.
*/
class WPBakeryShortCode_VulcanFlexbox extends WPBakeryShortCodesContainer
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_vulcanFlexbox_mapping' ) );
        add_shortcode( 'vc_vulcanFlexbox', array( $this, 'vc_vulcanFlexbox_html' ) );
    }
    
    public function vc_vulcanFlexbox_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
		array(
			'name' => __('Flexbox Container', 'text-domain'),
			'base' => 'vc_vulcanFlexbox',
			'description' => __('A Flexbox.', 'text-domain'), 
			'category' => __('Vulcan', 'text-domain'),   
			'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
			'content_element' => 'true',
			'is_container' => 'true',
			'as_parent' => array(
				'except' => ''
			) ,
			'params' => array(

				// Description
				/*array(
					'description' => __('<p>The Flexible Box Module, usually referred to as flexbox, was designed as a one-dimensional layout model, and as a method that could offer space distribution between items in an interface and powerful alignment capabilities.</p><br><a href="https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Flexible_Box_Layout/Basic_Concepts_of_Flexbox" target="_blank">Learn More</a>', 'mk_framework')
				),*/

				/*// Title
				array(
					'type' => 'textfield',
					'heading' => __('Box Title', 'mk_framework'),
					'param_name' => 'box_title',
					'value' => '',
					'description' => __('', "mk_framework")
				),

				// Flex Direction
				array(
					'type' => 'dropdown',
					'heading' => __('Direction', 'mk_framework'),
					'description' => __('The flex-direction CSS property specifies how flex items are placed in the flex container defining the main axis and the direction (normal or reversed).', 'mk_framework'),
					'param_name' => 'flex_direction',
					'value' => array(
						__('Row', 'mk_framework') => 'row',
						__('Row Reverse', 'mk_framework') => 'row-reverse',
						__('Column', 'mk_framework') => 'column',
						__('Column Reverse', 'mk_framework') => 'column-reverse'
					),
				),

				// Justify Content
				array(
					'type' => 'dropdown',
					'heading' => __('Justify Content', 'mk_framework'),
					'description' => __('The CSS justify-content property defines how the browser distributes space between and around content items along the main axis of their container.', 'mk_framework'),
					'param_name' => 'justify_content',
					'value' => array(
						__('Flex Start', 'mk_framework') => 'flex-start',
						__('Flex End', 'mk_framework') => 'flex-end',
						__('Center', 'mk_framework') => 'center',
						__('Space Between', 'mk_framework') => 'space-between',
						__('Space Around', 'mk_framework') => 'space-around',
						__('Space Evenly', 'mk_framework') => 'space-evenly',
						__('Stretch', 'mk_framework') => 'stretch',
						__('Start', 'mk_framework') => 'start',
						__('End', 'mk_framework') => 'end',
						__('Left', 'mk_framework') => 'left',
						__('Right', 'mk_framework') => 'right',
						__('Baseline', 'mk_framework') => 'baseline',
						__('First Baseline', 'mk_framework') => 'first baseline',
						__('Last Baseline', 'mk_framework') => 'last baseline',
					),
				),

				// Flex wrap
				array(
					'type' => 'dropdown',
					'heading' => __('Flex wrap', 'mk_framework'),
					'param_name' => 'flex_wrap',
					'description' => __('The CSS flex-wrap property specifies whether flex items are forced into a single line or can be wrapped onto multiple lines. If wrapping is allowed, this property also enables you to control the direction in which lines are stacked.', 'mk_framework'),
					'value' => array(
						__('No Wrap', 'mk_framework') => 'no-wrap',
						__('Wrap', 'mk_framework') => 'wrap',
						__('Wrap Reverse', 'mk_framework') => 'wrap-reverse',
					),
				),

				// Align Content
				array(
					'type' => 'dropdown',
					'heading' => __('Align Content', 'mk_framework'),
					'description' => __('The CSS align-content property defines how the browser distributes space between and around content items along the cross-axis of their container, which is serving as a flexible box container.', 'mk_framework'),
					'param_name' => 'align_content',
					'value' => array(
						__('Flex Start', 'mk_framework') => 'flex-start',
						__('Flex End', 'mk_framework') => 'flex-end',
						__('Center', 'mk_framework') => 'center',
						__('Space Between', 'mk_framework') => 'space-between',
						__('Space Around', 'mk_framework') => 'space-around',
						__('Space Evenly', 'mk_framework') => 'space-evenly',
						__('Stretch', 'mk_framework') => 'stretch',
						__('Start', 'mk_framework') => 'start',
						__('End', 'mk_framework') => 'end',
						__('Left', 'mk_framework') => 'left',
						__('Right', 'mk_framework') => 'right',
						__('Baseline', 'mk_framework') => 'baseline',
						__('First Baseline', 'mk_framework') => 'first baseline',
						__('Last Baseline', 'mk_framework') => 'last baseline',
					),

					'dependency' => array(
						'element' => 'flex_wrap',
						'value' => array(
							'wrap',
							'wrap-reverse'
						)
					)
				),

				// Align Items
				array(
					'type' => 'dropdown',
					'heading' => __('Align Items', 'mk_framework'),
					'description' => __('The CSS align-items property defines how the browser distributes space between and around flex items along the cross-axis of their container. This means it works like justify-content but in the perpendicular direction.', 'mk_framework'),
					'param_name' => 'align_items',
					'value' => array(
						__('Normal', 'mk_framework') => 'normal',
						__('Stretch', 'mk_framework') => 'stretch',
						__('Flex Start', 'mk_framework') => 'flex-start',
						__('Flex End', 'mk_framework') => 'flex-end',
						__('Center', 'mk_framework') => 'center',
						__('Self Start', 'mk_framework') => 'self-start',
						__('Self End', 'mk_framework') => 'self-end',
						__('Start', 'mk_framework') => 'start',
						__('End', 'mk_framework') => 'end',
						__('Left', 'mk_framework') => 'left',
						__('Right', 'mk_framework') => 'right',
						__('Baseline', 'mk_framework') => 'baseline',
						__('First Baseline', 'mk_framework') => 'first baseline',
						__('Last Baseline', 'mk_framework') => 'last baseline',
					),
					'dependency' => array(
						'element' => 'flex_wrap',
						'value' => array(
							'no-wrap'
						)
					)
				),*/

				// Custom classes
				array(
					'type' => 'textfield',
					'heading' => __('Extra class name', 'mk_framework'),
					'param_name' => 'el_class',
					'value' => '',
					'description' => __("If you wish to style particular content Flexbox differently, then use this field to add a class name and then refer to it in Custom CSS Shortcode or Masterkey Custom CSS option.", "mk_framework")
				)

			),
			'js_view' => 'VcColumnView'
		)
        );                                
        
    }
    
    public function vc_vulcanFlexbox_html( $atts, $content = '' )
	{
        
        extract(
            shortcode_atts(
                array(
					'el_class' => '',
                ), 
                $atts
            )
        );
		
		var_dump( $atts );

		return '<div class="vulcan-Flexbox" >' . do_shortcode( $content ) . '</div>';
		
    }
     
}

new WPBakeryShortCode_VulcanFlexbox();