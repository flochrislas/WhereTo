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
        $lunchTimeNow = Carbon::create(2017, 3, 19, 11, 30);
        Carbon::setTestNow($lunchTimeNow);
        $this->assertTrue(
            (new SuggestionController)->isLunchTime()
        );
        // Clear the mock
        Carbon::setTestNow();
    }

    public function testNotLunchTime() : void
    {
        // Mock now
        $lunchTimeNow = Carbon::create(2017, 3, 19, 11 ,29);
        Carbon::setTestNow($lunchTimeNow);
        $this->assertNotTrue(
            (new SuggestionController)->isLunchTime()
        );
        // Clear the mock
        Carbon::setTestNow();
    }
}