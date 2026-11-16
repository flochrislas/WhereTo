<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Admin dashboard actions.
 */
class AdminPanelController extends Controller
{
    /**
     * Switch the Debugbar ON or OFF for the current process.
     * The Debugbar is a dev dependency, so it may be absent (production).
     */
    public function debugModeSwitch(Request $request)
    {
        // The form sends mode=on or mode=off
        $on = $request->input('mode') === 'on';
        Log::debug('Debug mode '.($on ? 'ON' : 'OFF'));
        // Only touch the Debugbar when the package is installed and registered
        if (app()->bound('debugbar')) {
            $on ? app('debugbar')->enable() : app('debugbar')->disable();
        }

        // Back to the previous page (the dashboard)
        return back()->withInput();
    }
}
