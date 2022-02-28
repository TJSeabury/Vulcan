<?php namespace Vulcan\lib;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Illuminate/view
 *
 * Requires: illuminate/filesystem
 *
 * @source https://github.com/illuminate/view
 */
class Blade {
    private static $instance = null;

    public static function getInstance() {
        if ( null == self::$instance ) {
            self::$instance = new Blade();
        }
        return self::$instance;
    }

    // Configuration: Note that you can set several directories where your templates are located
    private $viewPaths = array();
    private $compiledViewsPath = '';
    private $filesystem = null;
    private $eventDispatcher = null;
    private $viewResolver = null;
    private $viewFinder = null;

    private $initialized = false;

    public function addViewPath( string $path ) {
        $this->viewPaths = array_unique( array_merge(
            $this->viewPaths,
            array( $path )
        ) );
        return $this->viewPaths;
    }

    private function __construct() {
        // Let's declare the default paths.
        $this->viewPaths[] = __DIR__ . '/views';
        $this->compiledViewsPath = __DIR__ . '/views/compiled';
    }

    public function init() {
        // Dependencies
        $this->filesystem = new \Illuminate\Filesystem\Filesystem;
        $this->eventDispatcher = new \Illuminate\Events\Dispatcher( 
            new \Illuminate\Container\Container
        );
        $this->viewResolver = new \Illuminate\View\Engines\EngineResolver;

        $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler(
            $this->filesystem,
            $this->compiledViewsPath
        );

        $this->viewResolver->register(
            'blade',
            function () use ( $bladeCompiler ) {
                return new \Illuminate\View\Engines\CompilerEngine(
                    $bladeCompiler
                );
            }
        );

        $this->viewFinder = new \Illuminate\View\FileViewFinder(
            $this->filesystem,
            $this->viewPaths
        );
        
        $this->initialized = true;
    }

    public function render( $viewName, $props ){
        if ( false === $this->initialized ) $this->init();
        
        // Create View Factory capable of rendering PHP and Blade templates
        $this->viewFactory = new \Illuminate\View\Factory(
            $this->viewResolver,
            $this->viewFinder,
            $this->eventDispatcher
        );

        // Return the view.
        return $this->viewFactory->make( $viewName, $props )->render();
    }
}