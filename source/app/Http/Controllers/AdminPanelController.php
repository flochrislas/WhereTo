<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPanelController extends Controller
{
    /**
     * Turn the Debugbar on or off for the current process (dev dependency, may be absent).
     */
    public function debugModeSwitch(Request $request)
    {
        $on = $request->input('mode') === 'on';
        Log::debug('Debug mode '.($on ? 'ON' : 'OFF'));
        if (app()->bound('debugbar')) {
            $on ? app('debugbar')->enable() : app('debugbar')->disable();
        }

        return back()->withInput();
    }
}
