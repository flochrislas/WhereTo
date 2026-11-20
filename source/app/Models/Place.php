<?php

namespace App\Models;

use App\Enums\BusinessStatus;
use App\Support\Position;

/**
 * What every kind of place (Restaurant, Store, Bar, Karaoke) offers to the code that handles them all
 * (App\Services\PlaceSearch). The methods come from the Location trait; getAttribute() from the Model.
 */
interface Place
{
    /** The coordinates as a Position, or null when the place has none */
    public function position(): ?Position;

    /** The distance from the visitor computed for this request (metres), or null when not computed */
    public function currentDistance(): ?float;

    public function setCurrentDistance(?float $distance): void;

    /** Is the place still in business (the business_status column, Operational when unset) */
    public function businessStatus(): BusinessStatus;

    /** The name in the page's language, followed by the status when the place is not operational, e.g. "Kindenmaru, permanently closed" */
    public function nameWithStatus(): string;

    /** The name of the place in this language (name_ja for 'ja'), or null when it has only its English name */
    public function translatedName(string $locale): ?string;

    /** The name to use alone in this language: the translated one when there is one, else the English one */
    public function nameIn(string $locale): string;

    /** Does the place have its own content in this language: always in English, in Japanese with a name_ja (Location trait) */
    public function hasContentIn(string $locale): bool;

    /** Should its page be indexed in this language: not gone for good, and content in that language */
    public function isIndexableIn(string $locale): bool;

    /**
     * The JSON-LD data of the standalone page of this place (schema.org vocabulary), built by App\Support\StructuredData
     *
     * @param  string  $url  the canonical URL of the page
     * @return array<string, mixed>
     */
    public function structuredData(string $url): array;

    /**
     * Any attribute of the model, e.g. getAttribute('name') (Illuminate\Database\Eloquent\Model)
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key);
}
