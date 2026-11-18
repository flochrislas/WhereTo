<?php

namespace App\Support;

use App\Models\Place;

/**
 * Builds the JSON-LD "structured data" of a place page: a small JSON block, in the schema.org vocabulary, that
 * tells search engines what the page describes (name, address, coordinates, links, our review). Google reads it
 * to show rich results (type, price range, stars). The visitor never sees it. See doc/10-seo.md, item 5.
 * Each model adds its own fields in its structuredData() method; this class holds what is common to all.
 */
final class StructuredData
{
    /**
     * The fields every kind of place has: type, name, url, the ward as address, coordinates and the known links.
     *
     * @param  string  $type  the schema.org type, e.g. 'Restaurant' or 'BarOrPub'
     * @param  string  $url  the canonical URL of the page
     * @param  array<string, mixed>  $extra  fields specific to the kind (price range, review...)
     * @return array<string, mixed>
     */
    public static function place(Place $place, string $type, string $url, array $extra = []): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $place->getAttribute('name'),
            'url' => $url,
        ];

        // Google wants a PostalAddress for a local business. Every place is in Shibuya ward, Tokyo (the premise of
        // the app), so that much is always true. No street line: the "location" column is a neighbourhood label
        // ("Shibuya, Dogenzaka", "near Stream"), not an address; the coordinates below give the exact spot.
        // A real street address would need its own column (doc/10-seo.md, item 7).
        $data['address'] = ['@type' => 'PostalAddress', 'addressLocality' => 'Shibuya', 'addressRegion' => 'Tokyo', 'addressCountry' => 'JP'];

        if ($position = $place->position()) {
            $data['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $position->lat, 'longitude' => $position->lon];
        }

        // "sameAs": other pages about the same place; the columns that do not exist on the model are null
        $links = array_filter([
            $place->getAttribute('google_maps_link'),
            $place->getAttribute('tabelog_link'),
            $place->getAttribute('official_website'),
        ], fn ($link) => is_string($link) && $link !== '');
        if ($links !== []) {
            $data['sameAs'] = array_values($links);
        }

        return array_merge($data, $extra);
    }

    /**
     * Our own review of the place as a schema.org Review (a single reviewer, not an aggregate of visitors),
     * or an empty array when the place was not scored.
     *
     * @param  string  $scoreColumn  the column of the main score, e.g. 'score_lunch'
     * @param  int  $best  the best possible score of that column (the scale), e.g. 4
     * @return array<string, mixed>
     */
    public static function review(Place $place, string $scoreColumn, int $best): array
    {
        $score = $place->getAttribute($scoreColumn);
        if (! is_numeric($score)) {
            return [];
        }

        $review = [
            '@type' => 'Review',
            'author' => ['@type' => 'Organization', 'name' => config('app.name')],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => (int) $score, 'bestRating' => $best, 'worstRating' => 0],
        ];

        // The real review date (the seeders set updated_at at every deploy, so it means nothing)
        $visitDate = $place->getAttribute('visit_date');
        if (is_string($visitDate) && $visitDate !== '') {
            $review['datePublished'] = substr($visitDate, 0, 10);
        }

        return ['review' => $review];
    }

    /**
     * The JSON text to print inside <script type="application/ld+json">. The flags encode < > & and quotes so
     * that no text from the database can close the script tag or break the page; Unicode and slashes stay
     * readable (the URLs).
     *
     * @param  array<string, mixed>  $data
     */
    public static function json(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
