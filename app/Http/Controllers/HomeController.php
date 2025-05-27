<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function switchLanguage($locale)
    {
        // Check if the locale is supported
        if (in_array($locale, ['en', 'ar'])) {
            // Store the locale in session
            Session::put('locale', $locale);

            // Set the application locale
            App::setLocale($locale);
        }

        // Redirect back to the previous page
        return redirect()->back();
    }
}
