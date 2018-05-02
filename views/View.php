<?php namespace Vulcan\views;

/**
 * Class View
 *
 * @package Vulcan\views
 */
/**
 * Class View
 *
 * @package Vulcan\views
 */
class View
{

	/**
	 * @var array
	 */
	protected $data;
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * View constructor.
	 *
	 * @param string $group
	 * @param string $type
	 * @param array  $data
	 */
	public function __construct( string $group, string $type, array $data = array() )
	{
        $this->data = $data;
		switch ( $group )
		{
			case 'admin':
				$this->path = __DIR__ . '/admin/' . $type . '.php';
				break;
			case 'header':
				break;
			case 'footer':
				break;
		}
    }

	/**
	 * @return string
	 * @throws \Vulcan\utils\VulcanException
	 */
	public function render()
	{
        if ( file_exists( $this->path ) )
		{
            extract( $this->data );
            ob_start();
            include $this->path;
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }
		else
		{
            throw new \Vulcan\utils\VulcanException( 'View not found.' );
        }
    }
	
}