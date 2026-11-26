<?php

namespace Tests\Unit;

use App\Enums\BusinessStatus;
use Tests\TestCase;

/** Tests\TestCase (the application), not PHPUnit's: the labels are translated with __() */
class BusinessStatusTest extends TestCase
{
    public function test_only_operational_is_open(): void
    {
        $this->assertTrue(BusinessStatus::Operational->isOpen());
        $this->assertFalse(BusinessStatus::ClosedTemporarily->isOpen());
        $this->assertFalse(BusinessStatus::ClosedPermanently->isOpen());
        $this->assertFalse(BusinessStatus::Replaced->isOpen());
        $this->assertFalse(BusinessStatus::Relocated->isOpen());
    }

    public function test_labels_and_gone_for_good(): void
    {
        $this->assertSame('', BusinessStatus::Operational->label());
        $this->assertSame('temporarily closed', BusinessStatus::ClosedTemporarily->label());
        $this->assertSame('permanently closed', BusinessStatus::ClosedPermanently->label());
        $this->assertSame('replaced by another business', BusinessStatus::Replaced->label());
        $this->assertSame('moved to another address', BusinessStatus::Relocated->label());
        // In the language of the page
        app()->setLocale('ja');
        $this->assertSame('閉店', BusinessStatus::ClosedPermanently->label());
        $this->assertSame('移転しました', BusinessStatus::Relocated->label());
        app()->setLocale('en');
        // Gone for good (noindex): permanently closed, replaced and relocated only
        $this->assertSame([BusinessStatus::ClosedPermanently, BusinessStatus::Replaced, BusinessStatus::Relocated], array_values(array_filter(BusinessStatus::cases(), fn ($s) => $s->isGone())));
    }

    public function test_from_request_falls_back_to_operational(): void
    {
        $this->assertSame(BusinessStatus::Replaced, BusinessStatus::fromRequest('REPLACED'));
        $this->assertSame(BusinessStatus::Relocated, BusinessStatus::fromRequest('RELOCATED'));
        $this->assertSame(BusinessStatus::Replaced, BusinessStatus::fromRequest(BusinessStatus::Replaced));
        // A place created in PHP has no value until it is reloaded from the DB
        $this->assertSame(BusinessStatus::Operational, BusinessStatus::fromRequest(null));
        $this->assertSame(BusinessStatus::Operational, BusinessStatus::fromRequest('closed'));
    }
}
