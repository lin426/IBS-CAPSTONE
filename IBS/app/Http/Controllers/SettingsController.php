<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    public function index()
    {
        // Add fields later (name/email/password change, etc.)
        return view('settings.index');
    }
}
