<?php namespace Vulcan\lib\models\api;

class API extends \Vulcan\lib\ASingleton {

    protected array $config;

    protected function __construct() {
        // Apply default config.
        $this->setConfig( \Vulcan\lib\models\api\Config::$defaults );
        $this->routes = [];
    }

    public function setConfig( array $config ):void {
        if ( !isset( $this->config ) || empty( $this->config ) ) {
            $this->config = \Vulcan\lib\models\api\Config::$defaults;
        }
        foreach ( $config as $setting => $value ) {
            if ( \Vulcan\lib\models\api\Config::hasSetting( $setting ) ) {
                $this->config[$setting] = $value;
            }
            else {
                throw new \Error( "API has no such setting '$setting'!" );
            }
        }
        
    }

    public function init(): void {
        if ( true === $this->config['requireAuth'] ) {
            $this->requireAuth();
        }
        $this->initializeRoutes( $this->routes );
    }

    protected function requireAuth(): void {
        \add_filter(
            'rest_authentication_errors',
            function( $result ) {
                // If a previous authentication check was applied,
                // pass that result along without modification.
                if ( true === $result || \is_wp_error( $result ) ) {
                    return $result;
                }
            
                // No authentication has been performed yet.
                // Return an error if user is not logged in.
                if ( ! is_user_logged_in() ) {
                    return new \WP_Error(
                        'rest_not_logged_in',
                        __( 'You are not currently logged in.' ),
                        array( 'status' => 401 )
                    );
                }
            
                // Our custom authentication check should have no effect
                // on logged-in requests
                return $result;
            }
        );
    }

    public function registerRoutes( array $routes ): void {
        if ( empty( $routes ) ) throw new \Error( 'Routes must not be empty.' );
        foreach ( $routes as $route ) {
            if ( $route instanceof \Vulcan\lib\models\api\Route ) {
                // register the route
                $this->routes[] = $route;
            }
            else {
                throw new \Error( "Invalid type in \$routes! Recieved: $route" );
            }
        }
    }

    protected function initializeRoutes( array $routes ): void {
        if ( empty( $routes ) ) {
            throw new \Error( 'Unable to register nothing.' );
        }
        \add_action(
            'rest_api_init',
            function () {
                foreach( $routes as $route ) {
                    \register_rest_route(
                        "{$this->config['namespace']}/{$this->config['version']}",
                        $route->name,
                        $route->register()
                    );
                }
            }
        );
    }

}




function lipsum( $words ) {
    $response = \wp_remote_get("https://www.lipsum.com/feed/json?what=words&amount=$words&start=false");
    $response = json_decode( $response['body'] );
    return $response->feed->lipsum;
}



