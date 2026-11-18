<?php

namespace Tests\Unit;

use App\Enums\BusinessStatus;
use PHPUnit\Framework\TestCase;

class BusinessStatusTest extends TestCase
{
    public function test_only_operational_is_open(): void
    {
        $this->assertTrue(BusinessStatus::Operational->isOpen());
        $this->assertFalse(BusinessStatus::ClosedTemporarily->isOpen());
        $this->assertFalse(BusinessStatus::ClosedPermanently->isOpen());
        $this->assertFalse(BusinessStatus::Replaced->isOpen());
    }

    public function test_labels_and_gone_for_good(): void
    {
        $this->assertSame('', BusinessStatus::Operational->label());
        $this->assertSame('temporarily closed', BusinessStatus::ClosedTemporarily->label());
        $this->assertSame('permanently closed', BusinessStatus::ClosedPermanently->label());
        $this->assertSame('replaced by another business', BusinessStatus::Replaced->label());
        // Gone for good (noindex): permanently closed and replaced only
        $this->assertSame([BusinessStatus::ClosedPermanently, BusinessStatus::Replaced], array_values(array_filter(BusinessStatus::cases(), fn ($s) => $s->isGone())));
    }

    public function test_from_request_falls_back_to_operational(): void
    {
        $this->assertSame(BusinessStatus::Replaced, BusinessStatus::fromRequest('REPLACED'));
        $this->assertSame(BusinessStatus::Replaced, BusinessStatus::fromRequest(BusinessStatus::Replaced));
        // A place created in PHP has no value until it is reloaded from the DB
        $this->assertSame(BusinessStatus::Operational, BusinessStatus::fromRequest(null));
        $this->assertSame(BusinessStatus::Operational, BusinessStatus::fromRequest('closed'));
    }
}
