<?php

namespace App\Traits;

use App\Enums\BusinessStatus;
use App\Support\LocaleUrl;
use App\Support\Position;
use App\Tools\GeoUtils;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Localisation related stuff shared by the place models (restaurants, stores, bars, karaoke),
 * and their business status (open or closed, doc/11-data-freshness.md).
 * A trait is a set of methods and properties copied into every class that "use"s it.
 */
trait Location
{
    /** Storing the calculated distance from the visitor, in metres. Computed per request, not a DB column. */
    public ?float $currentDistance = null;

    /** The coordinates as a Position, or null when the place has none */
    public function position(): ?Position
    {
        if ($this->lat === null || $this->lon === null) {
            return null;
        }

        // The DB returns decimals as strings, hence the (float) casts
        return new Position((float) $this->lat, (float) $this->lon);
    }

    /**
     * Local scope: only the places still in business. #[Scope] makes it callable on a query as ->open(),
     * e.g. Restaurant::query()->open(). Local, not global: the standalone page still loads a closed place.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('business_status', BusinessStatus::Operational->value);
    }

    /**
     * Is the place still in business. Read from the raw column value, not through a cast: an unknown value (a typo
     * such as 'CLOSED' in a seeder) must not break the page. Such a value counts as Operational here, so the page shows
     * the place as open, while the open() scope (business_status = 'OPERATIONAL') hides it from the lists.
     * A place created in PHP without the key (a test) has no value at all until reloaded: Operational too.
     */
    public function businessStatus(): BusinessStatus
    {
        // getAttributes(): the raw column values, before any cast or accessor
        return BusinessStatus::fromRequest($this->getAttributes()['business_status'] ?? null);
    }

    /** The name, followed by the status when the place is not operational, e.g. "Kindenmaru, permanently closed" */
    public function nameWithStatus(): string
    {
        $status = $this->businessStatus();

        // ":name, :status" is a sentence of lang/<locale>.json, so a language can write it its own way
        return $status->isOpen() ? (string) $this->getAttribute('name') : __(':name, :status', ['name' => (string) $this->getAttribute('name'), 'status' => $status->label()]);
    }

    /**
     * Does the place have its own content in this language (doc/12-multilingual-ui.md): always in the default
     * language (the names and notes are English). There is no name_ja yet, so no other language has any; when
     * the seeders get one, this becomes e.g. "|| ($locale === 'ja' && $this->getAttribute('name_ja'))".
     */
    public function hasContentIn(string $locale): bool
    {
        return $locale === LocaleUrl::default();
    }

    /**
     * Should the search engines index the page of the place in this language: not when the place is gone for good
     * (permanently closed, replaced: nothing left to rank, in any language), nor in a language it has no content in
     * (the page would be a thin copy of the English one). The page is then noindex and in no hreflang list.
     */
    public function isIndexableIn(string $locale): bool
    {
        return ! $this->businessStatus()->isGone() && $this->hasContentIn($locale);
    }

    public function currentDistance(): ?float
    {
        return $this->currentDistance;
    }

    public function setCurrentDistance(?float $distance): void
    {
        $this->currentDistance = $distance;
    }

    /**
     * Simply fill lat and lon from a string holding both ("lat, lon"), then save.
     */
    public function fillCoordinatesFromString(string $coordinates): void
    {
        try {
            // Parse the string into a Position (throws when it is not "lat, lon")
            $position = Position::fromString($coordinates);
            $this->lat = $position->lat;
            $this->lon = $position->lon;
            // Write the model to the DB
            $this->save();
        } catch (InvalidArgumentException $e) {
            // Not a "lat, lon" string: keep the current coordinates
            Log::debug('Invalid coordinates for '.class_basename($this).' '.$this->id.': '.$e->getMessage());
        }
    }

    /**
     * Try to fill lat and lon with the ones found in the Google Maps link, then save.
     * NOTE: the link's coordinates are actually the centre of the map, NOT the location we want.
     */
    public function fillCoordinatesFromGoogleLink(): void
    {
        try {
            // (string) turns a null link into '' instead of a type error
            $coord = GeoUtils::google2coord((string) $this->google_maps_link);
            $this->lat = $coord[0];
            $this->lon = $coord[1];
            $this->save();
        } catch (InvalidArgumentException $e) {
            Log::debug('Could not find coordinates in the Google Maps link of '.class_basename($this).' '.$this->id);
        }
    }
}
