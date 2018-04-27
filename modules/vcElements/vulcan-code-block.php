<?php namespace Vulcan\vcElements;

/*
* Vulcan Code Block.
* Syntax highlights your code.
*/
class vcCodeBlock extends WPBakeryShortCode
{
    
    function __construct() {
        add_action( 'init', array( $this, 'vc_CodeBlock_mapping' ) );
        add_action(
            'wp_enqueue_scripts',
            function()
            {
                $path = get_stylesheet_directory_uri() . '/assets/js/prism.js';
                wp_enqueue_script( 'vulcan-prism-js', $path, array(), utils\FileTools::getVersion( $path ), true );
            },
            10
        );
		add_filter(
            'script_loader_tag',
            function( $tag, $handle, $src )
            {
                if ( 'vulcan-prism-js' === $handle ) {
                    $tag = '<script type="text/javascript" src="' . esc_url( $src ) . '" data-manual ></script>';
                } 
            return $tag;
        },
        10,
        3
    ); 
        
        add_shortcode( 'vc_CodeBlock', array( $this, 'vc_CodeBlock_render' ) );
    }
    
    public function vc_CodeBlock_mapping()
	{
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Code Block', 'text-domain'),
                'base' => 'vc_CodeBlock',
                'description' => __('Syntax highlights your code.', 'text-domain'), 
                'category' => __('Vulcan', 'text-domain'),   
                'icon' => get_stylesheet_directory_uri() . '/assets/media/vulcan-icon.png',
                'params' => array(

                    array(
                        'type' => 'dropdown',
                        'heading' => 'Language',
                        'param_name' => 'langauge',
                        'value' => array(
                            __( 'HTML', 'mk_framework' ) => 'html',
                            __( 'CSS', 'mk_framework' ) => 'css',
                            __( 'JS', 'mk_framework' ) => 'js',
                            __( 'PHP', 'mk_framework' ) => 'php'
                        ),
                        'description' => ''
                    ),

					array(
						"type" => "textarea_raw_html",
						"heading" => __("Raw code", "mk_framework"),
						"param_name" => "code",
						"value" => "",
						"description" => __("Code to Highlight", "mk_framework")
					)
                        
                )
            )
        );                                
        
    }
    
    public function vc_CodeBlock_render( $atts )
	{
        
        extract(
            shortcode_atts(
                array(
                    'language' => 'html',
                    'code' => ''
                ), 
                $atts
            )
        );
		
		ob_start();
		?>
        <script>
        Prism.highlightAll();
        </script>
		<div class="vulcan-code-block">
            <pre>
                <code class="<?php echo 'language-' . $language; ?>"><?php echo $code; ?></code>
            </pre>
        </div>
		<?php
		return ob_get_clean();
		
    }
     
}

new vcCodeBlock();