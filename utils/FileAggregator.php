<?php namespace Vulcan\utils;

class FileAggregator
{
	/*
	* Retrieves the files contents as if outputting to a client,
	* i.e., server side script files are first processed.
	* @param string $readPath - The absolute path to the folder.
	* @return array
	*/
	public static function getFiles( string $readPath, array $extensions, bool $pathOnly )
	{
		$r = array();
		foreach( array_filter( glob( $readPath . '/*.*' ), 'is_file' ) as $file )
			{
				$fn = pathinfo( $file );
				if ( in_array( $fn['extension'], $extensions, true ) && $fn['filename'] !== 'index' )
				{
					if ( $pathOnly )
					{
						$r[] = $fn['dirname'] . '/' . $fn['basename'];
					}
					else
					{
						$r[] = self::get_file_output( $file );
					}
				}
			}
		return $r;
	}
	
	/*
	* Aggregate parts and write to directory.
	* @param array $f - The file content to aggregate and write.
	* @param string $writePath - The directory path to write to.
	* @param string $filename - The name of the file.
	* @return void
	*/
	public static function write( string $c, string $writePath, string $filename )
	{
		if ( is_writeable( $writePath ) )
		{
			$f = fopen( $writePath . $filename, 'wb' );
			fwrite( $f, $c );
			fclose( $f );
		}
	}
	
	/*
	* Combines css parts.
	* @param array $files - The css files content.
	* @return string
	*/
	public static function agg( array $files )
	{
		return implode( "\n\n", $files );
	}
	
	/*
	* Remove comments and minify css.
	* @param string $c - An array of strings from files, assumed to be css.
	* @return string
	*/
	public static function minify( string $c )
	{
		return str_replace( 
			array( ' {', ': ', ', ', ' >', '> ', ' > ', ' !', '( ', ' )' ), 
			array( '{', ':', ',', '>', '>', '>', '!', '(', ')' ), 
			str_replace( 
				array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ),
				'',
				preg_replace( 
					'!/\*[^*]*\*+([^/][^*]*\*+)*/!', 
					'', 
					$c 
				) 
			) 
		);
	}
	
	/*
	* Returns output buffered content of included files.
	* @param string $file - The path to the file.
	* @return string
	*/
	private function get_file_output( string $file )
	{
		ob_start();
		include( $file );
		return ob_get_clean();
	}
	
}