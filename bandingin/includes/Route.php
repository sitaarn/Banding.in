<?php
/**
 * =====================================================
 * HELPER FUNCTIONS - Route Web
 * Praktikum Aplikasi Web - Universitas Tidar
 * =====================================================
 */

namespace Includes;

class Route {

    protected $registerMiddleware;
    private $middlewarePassed;


    public function __construct () {
        $this->registerMiddleware = [
            'login' => function () { return \isLoggedIn(); },
            'guest' => function () { return \isGuest(); },
            'admin' => function () { return \isAdmin(); },
            'all' => function () { return true; }
        ];   
    }

    public function get($uri, $action) {

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if(!$this->middlewarePassed) return false;


        if ( $_SERVER['REQUEST_METHOD'] !== 'GET' || $requestUri !==  \FIRSTSECTION_URI . $uri ) return false;
        // action 0 itu objek dan action 1 itu method
        call_user_func_array([new $action[0], $action[1]], []);
    }

    public function post($uri, $action) {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


        if(!$this->middlewarePassed) return false;

        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || $requestUri !==  \FIRSTSECTION_URI . $uri ) return false;
        call_user_func_array([new $action[0], $action[1]], []);
    }

    public function group($action) {
        $action($this);
    }

    public function middleware($middleware) {
        $this->middlewarePassed = true;
        
        foreach ($middleware as $name) {
            $condition = $this->registerMiddleware[$name]();
            if (!$condition) {
                $this->middlewarePassed = false;
                break;
            }
        }
        return $this;
    }


}