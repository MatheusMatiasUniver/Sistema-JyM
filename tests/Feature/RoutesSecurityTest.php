<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutesSecurityTest extends TestCase
{
    public function test_login_route_has_throttle_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('login.submit');
        $this->assertNotNull($route);
        $action = $route->getAction();
        $middleware = $action['middleware'] ?? [];
        $this->assertTrue(in_array('throttle:login', $middleware));
    }

    public function test_face_routes_have_throttle_middleware(): void
    {
        $routes = [
            '/face/register',
            '/face/authenticate',
            '/face/authenticate-code',
            '/face/set-kiosk-registering',
        ];

        foreach ($routes as $uri) {
            $route = collect(app('router')->getRoutes())->first(function ($r) use ($uri) {
                return $r->uri() === ltrim($uri, '/');
            });
            $this->assertNotNull($route);
            $action = $route->getAction();
            $middleware = $action['middleware'] ?? [];
            $this->assertTrue(in_array('throttle:face', $middleware));
        }
    }
}