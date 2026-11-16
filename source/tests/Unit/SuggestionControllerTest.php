<?php

namespace Tests\Unit;

use App\Http\Controllers\SuggestionController;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SuggestionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_lunch_time_starts_at_11_30_tokyo(): void
    {
        Carbon::setTestNow(Carbon::create(2017, 3, 19, 11, 30, 0, 'Asia/Tokyo'));
        $this->assertTrue(SuggestionController::isLunchTime());
    }

    public function test_lunch_time_ends_at_14_00_tokyo(): void
    {
        Carbon::setTestNow(Carbon::create(2017, 3, 19, 14, 0, 0, 'Asia/Tokyo'));
        $this->assertTrue(SuggestionController::isLunchTime());
        Carbon::setTestNow(Carbon::create(2017, 3, 19, 14, 1, 0, 'Asia/Tokyo'));
        $this->assertFalse(SuggestionController::isLunchTime());
    }

    public function test_not_lunch_time(): void
    {
        Carbon::setTestNow(Carbon::create(2017, 3, 19, 11, 29, 0, 'Asia/Tokyo'));
        $this->assertFalse(SuggestionController::isLunchTime());
    }
}
