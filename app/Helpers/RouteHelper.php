<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class RouteHelper
{
    /**
     * Check if the current route is active
     */
    public static function setActive($route)
    {
        if (is_array($route)) {
            return in_array(Request::path(), $route) ? 'active' : '';
        }
        return Request::path() == $route ? 'active' : '';
    }
} 