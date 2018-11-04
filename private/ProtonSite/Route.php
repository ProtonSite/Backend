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
        // Loop throuh all routes
        foreach( Route::$ROUTES as $route ) {
            // Variable to keep track of matches found.
            $matches = 0;
            // Loop though all requested options, separating key from value.
            foreach( $options as $option_key => $option_value ) {
                // Check if current route iteration has an option of the same name set.
                if( array_key_exists( $option_key, $route ) ) {
                    // Does that option match the requested value?
                    if($route[$option_key] == $option_value) {
                        // Increment matches found by one.
                        $matches++;
                    // Is the requested option set as an array? Is the requested option value in that array?
                    } else if( is_array( $route[$option_key] ) && in_array( $option_value, $route[$option_key] ) ) {
                        // Increment matches found by one.
                        $matches++;
                    }
                }
            }
            // Have we found a route with the exact options we're looking for?
            if( count( $options ) == $matches ) {
                // Return the route's options.
                return $route;
            }
        }
        // Route not found, return false.
        return false;
    }

    /**
     * Fetch the route matching the provided options. Optionally you can request HTTPS be returned "on" or not (anything else), default is "auto" (use current request).
     * @param array $options Key => Value paired array of options to match when finding the route.
     * @param string $https Defines whether to use HTTPS.
     * @return string The final Full URL for the requested route.
     */
    public static function url(array $options, $https = "auto") {
        // Fetch the route matching the requested options.
        $route = Route::fetch($options);
        // Does the route have a 'host' option set?
        if( !empty( $route ) && array_key_exists('host', $route) ) {
            // Should the host option be an array, use the first host, otherwise just use the host.
            $host = is_array($route['host']) ? $route['host'][0] : $route['host'];
            // What HTTPS option should we use?
            if( $https == "auto" ) {
                // Auto - Return URL matching current connection for HTTPS/HTTP
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $host . '/' . $route['uri'];
            } else if( $https == "on" ) {
                // On - Return URL using HTTPS
                return "https://" . $host . '/' . $route['uri'];
            } else {
                // Anything else - Return URL using HTTP
                return "http://" . $host . '/' . $route['uri'];
            }
        // Route has no host. Use current host.
        } else {
            // Which HTTPS option should we use?
            if( $https == "auto" ) {
                // Auto - Return URL matching current connection for HTTPS/HTTP
                return "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            } else if( $https == "on" ) {
                // On - Return URL using HTTPS
                return "https://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            } else {
                // Anything else - Return URL using HTTP
                return "http://" . $_SERVER['HTTP_HOST'] . '/' . $route['uri'];
            }
        }
    }

}