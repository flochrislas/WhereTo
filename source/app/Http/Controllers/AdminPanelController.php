<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    /**
     * Turn on/off DEBUG mode
     *
     * @return \Illuminate\Http\Response
     */
    public function debugModeSwitch(Request $request)
    {
        $switch = request('mode'));
        if ($switch == 'on') {
            $this->debugModeOn();
        }
        else {
            $this->debugModeOff();
        }
        // Redirect the user to where he was
        return back()->withInput();
    }

    private function debugModeOn() : void
    {
        \Log::debug('Debug mode ON!');
        \Debugbar::enable();
    }

    private function debugModeOff() : void
    {
        \Log::debug('Debug mode OFF!');
        \Debugbar::disable();
    }
}
