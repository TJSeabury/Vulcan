<?php 

class VulcanButton extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct(array(
            'name'            => __( 'Vulcan Button', '_vulcan' ),
            'description'     => __( 'A button!', '_vulcan' ),
            'group'           => __( 'Vulcan', '_vulcan' ),
            'category'        => __( 'Basic', '_vulcan' ),
            'dir'             => __DIR__,
            'url'             => VULCANTHEMEROOT . '/builder_modules/VulcanButton/',
            'icon'            => '',
            'editor_export'   => true, // Defaults to true and can be omitted.
            'enabled'         => true, // Defaults to true and can be omitted.
            'partial_refresh' => false, // Defaults to false and can be omitted.
        ));
    }
}

FLBuilder::register_module( 'VulcanButton', array(
	'general' => array(
        'title' => __( 'General', '_vulcan' ),
        'sections' => array(
            'content' => array(
                'title' => __( 'Content', '_vulcan' ),
                'fields' => array(
                    'button_text' => array(
                        'type' => 'text',
                        'label' => __( 'Button Text', '_vulcan' ),
                    ),
                    'button_link' => array(
						'type'          => 'link',
						'label'         => __('Button Link', '_vulcan')
					),
					'button_icon' => array(
						'type'          => 'icon',
						'label'         => __( 'Button Icon', '_vulcan' ),
						'show_remove'   => true
					),
					'open_in_new_tab' => array(
						'type' => 'vulcan-toggle',
						'label' => 'Open in new tab?',
						'default' => ''
					)
                )
            ),
			'style' => array(
				'title' => __( 'Style', '_vulcan' ),
				'fields' => array(
					'border_radius' => array(
						'type' => 'vulcan-range',
						'label' => 'Border Radius',
						'default' => 0,
						'min' => 0,
						'max' => 30,
						'step' => 1
					),
					'button_text_color' => array(
						'type' => 'color',
						'label' => __( 'Text Color', '_vulcan' ),
						'default' => '333333',
						'show_reset' => true,
						'show_alpha' => true
					),
					'button_background_color' => array(
						'type' => 'color',
						'label' => __( 'Background Color', '_vulcan' ),
						'default' => '333333',
						'show_reset' => true,
						'show_alpha' => true
					),
					'button_alignment' => array(
						'type'          => 'select',
						'label'         => __( 'Button Alignment', '_vulcan' ),
						'default'       => 'left',
						'options'       => array(
							'left'      => __( 'Left', '_vulcan' ),
							'center'      => __( 'Center', '_vulcan' ),
							'right'      => __( 'Right', '_vulcan' ),
						)
					)
				)
			)
        )
    )
) );