<?php namespace Vulcan\utils;

class FileVersion
{
	/*
	* @param string $url The full file url.
	* @return int
	*/
	public static function getVersion( string $url )
	{
		$content_url = content_url();
		$filepath    = str_replace( $content_url, WP_CONTENT_DIR, $url );
		$filepath    = explode( '?', $filepath );
		$filepath    = array_shift( $filepath );
		// Ensure the file actually exists.
		if ( ! file_exists( $filepath ) ) {
			return;
		}
		// Attempt to read the file timestamp.
		try {
			$timestamp = filemtime( $filepath );
		} catch ( \Exception $e ) {
			return;
		}
		return $timestamp;
	}
	
	/*
	* Compares the ages of files against a reference file.
	* @param string $r1 - The reference file.
	* @param array $r2 - The files to check.
	* @return bool
	*/
	public static function comparator( string $r1, array $r2 )
	{
		$r1 = filemtime( $r1 );
		foreach ( $r2 as $r )
		{
			$rTime = filemtime( $r );
			if ( $r1 <= filemtime( $r ) )
			{
				return true;
			}
		}
		return false;
	}
}