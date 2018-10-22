<?php

namespace ProtonSite;

class Route {

    private static $ROUTES = [];

    /**
     * Register a route.
     * @param String $method GET|POST
     * @param String $uri The URI without leading slash. (e.g. page)
     * @param callable $action The callable action to perform.
     * @param array $options (Optional) Any additional options you wish to register with the route.
     */
    public static function register(String $method, String $uri, callable $action, array $options = []) {
        $valid_methods = ['GET', 'POST'];
        
        if(!in_array($method, $valid_methods))
            $method = 'GET';

        $route = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];

        $restricted_options = array_keys($route);

        if(!empty($options)) {
            foreach($options as $option_key => $option_value) {
                if(!in_array($option_key, $restricted_options)) {
                    $route[$option_key] = $option_value;
                }
            }
        }

        Route::$ROUTES[] = $route;
    }

    /**
     * Alias of register() without the method parameter (method=GET).
     * Register a route.
     * @param String $uri The URI without leading slash. (e.g. page)
     * @param callable $action The callable action to perform.
     * @param array $options (Optional) Any additional options you wish to register with the route.
     */
    public static function get(String $uri, callable $action, array $options = []) {
        Route::register('GET', $uri, $action, $options);
    }

    /**
     * Alias of register() without the method parameter (method=POST).
     * Register a route.
     * @param String $uri The URI without leading slash. (e.g. page)
     * @param callable $action The callable action to perform.
     * @param array $options (Optional) Any additional options you wish to register with the route.
     */
    public static function post(String $uri, callable $action, array $options = []) {
        Route::register('POST', $uri, $action, $options);
    }

    /**
     * Fetch any route based on the options defined with the route.
     * Basically anything can be used here, as long as its part of the route you wish to fetch.
     * @param array $options Key => Value paired array of options to match.
     * @return array All Key => Value options registered with the route.
     */
    public static function fetch(array $options) {
        foreach( Route::$ROUTES as $route ) {
            $matches = 0;
            foreach( $options as $option_key => $option_value ) {
                if( array_key_exists( $option_key, $route ) && $route[$option_key] == $option_value ) {
                    $matches++;
                }
            }

            if( count( $options ) == $matches ) {
                return $route;
            }
        }

        return false;
    }

    /**
     * Fetch the route matching the provided options. Optionally you can request HTTPS be returned "on" or not (anything else), default is "auto" (use current request).
     * @param array $options Key => Value paired array of options to match when finding the route.
     * @param string $https Defines whether to use HTTPS.
     * @return string The final Full URL for the requested route.
     */
    public static function url(array $options, $https = "auto") {
        $route = Route::fetch($options);

        if( !empty( $route ) && array_key_exists('host', $route) ) {
            $host = is_array($route['host']) ? $route['host'][0] : $route['host'];

            if( $https == "auto" ) {
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $host . '/' . $route['uri'];
            } else if( $https == "on" ) {
                return "https://" . $host . '/' . $route['uri'];
            } else {
                return "http://" . $host . '/' . $route['uri'];
            }
        } else {
            if( $https == "auto" ) {
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            } else if( $https == "on" ) {
                return "https://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            } else {
                return "http://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            }
        }
    }

}