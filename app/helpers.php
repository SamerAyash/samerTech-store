<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('adminAuth')) {
    function adminAuth()
    {
        return Auth::guard('admin');
    }
}


if (!function_exists('admin')) {
    function admin()
    {
        return adminAuth()->user();
    }
}


if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return adminAuth()->check();
    }
}


if (!function_exists('userAuth')) {
    function userAuth()
    {
        return Auth::guard('web');
    }
}


if (!function_exists('user')) {
    function user()
    {
        return userAuth()->user();
    }
}

if (!function_exists('isUser')) {
    function isUser()
    {
        return userAuth()->check();
    }
}

if (!function_exists('arabic_slug')) {

    function arabic_slug($string = null, $separator = "-") 
    {
        if (is_null($string)) {
            return "";
        }
        $string = trim($string);
        $string = mb_strtolower($string, "UTF-8");
        $string = preg_replace("/[^a-z0-9_\s\-\x{0600}-\x{06FF}]/u", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", $separator, $string);
        return $string;
    }
}

if (!function_exists('get_api_locale')) {
    /**
     * Get locale from X-Locale header or default to 'en'
     * 
     * @param \Illuminate\Http\Request|null $request
     * @return string 'en' or 'ar'
     */
    function get_api_locale($request = null)
    {
        if (!$request) {
            $request = request();
        }
        
        $locale = $request->header('X-Locale');
        
        // Validate locale - only allow 'en' or 'ar'
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Default to 'en'
        }
        
        return $locale;
    }
}

if (!function_exists('notifyAdmins')) {
    /**
     * Send notification to all admin users
     * 
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    function notifyAdmins($notification)
    {
        \App\Models\Admin::chunk(100, function ($admins) use ($notification) {
            foreach ($admins as $admin) {
                $admin->notify($notification);
            }
        });
    }
}