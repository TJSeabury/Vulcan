<?php namespace Vulcan\interfaces\ajax;

/*
* Enables ajax for requesting shortcode processing on the clientside.
*/
class AjaxShortcodes
{
	public static function enable()
	{
		add_action(
			'wp_ajax_do_shortcode',
			'ajax_do_shortcode'
		);

		add_action(
			'wp_ajax_nopriv_do_shortcode',
			'ajax_do_shortcode'
		);

		function ajax_do_shortcode()
		{
			$output;
			switch( $_REQUEST['fn'] )
			{
				case 'do_shortcode':
					$output = do_shortcode( wp_unslash( $_REQUEST['shortcode'] ) );
					break;
				default:
					$output = 'Invalid shortcode';
					break;
			}
			$output = json_encode( $output );
			echo $output;
			wp_die();
		}
	}
}