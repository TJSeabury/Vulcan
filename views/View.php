<?php namespace Vulcan\views;

class View
{
    protected $path, $data;

    public function __construct( string $path, array $data = array() )
	{
        $this->path = $path;
        $this->data = $data;
    }

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
            //Throws exception
        }
    }
	
}