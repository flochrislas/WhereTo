<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use App\Http\Controllers\SuggestionController;

class SuggestionControllerTest extends TestCase
{
    public function testLunchTime() : void
    {
        // Mock now
        $lunchTimeNow = Carbon::create(2017, 3, 19, 12);
        Carbon::setTestNow($lunchTimeNow);
        $this->assertEquals(
            "It's about lunch time!",
            (new SuggestionController)->suggestLunch(null)
        );
        // Clear the mock
        Carbon::setTestNow();
    }

    public function testNotLunchTime() : void
    {
        // Mock now
        $lunchTimeNow = Carbon::create(2017, 3, 19, 18);
        Carbon::setTestNow($lunchTimeNow);
        $this->assertEquals(
            "",
            (new SuggestionController)->suggestLunch(null)
        );
        // Clear the mock
        Carbon::setTestNow();
    }
}