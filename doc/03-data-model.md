# Data model

The schema is defined by the migrations in `source/database/migrations/`. The real content of the app is in the seeders: there is no admin UI (removed in November 2026), and data edits are committed as "data update" commits to the seeders.

## Tables

### Places

All four place tables share the same core: `id`, `created_at`, `updated_at`, `name`, `location` (a free-text area name such as "Shibuya"), `lat`, `lon` (nullable `decimal(10,7)`; they were floats before the upgrade), `points` (short summary shown in the list, truncated to 50 characters), `experience` (long personal review), `google_maps_link`, `official_website`, `business_status`, `google_place_id`.

`business_status` (string, not null, default `OPERATIONAL`) says whether the place is still in business. The `App\Enums\BusinessStatus` enum gives its meaning, through `businessStatus()` of the `Location` trait (no Eloquent cast: the method reads the raw value and turns an unknown one into `OPERATIONAL`, so a typo in a seeder never breaks a page; the `open()` scope still hides such a place from the lists): `OPERATIONAL`, `CLOSED_TEMPORARILY`, `CLOSED_PERMANENTLY` (Google's Places API values) or `REPLACED` (another business is at the address). `isOpen()` is true for `OPERATIONAL` only, `label()` gives the wording of the page ("permanently closed", "temporarily closed", "replaced by another business", empty for an open place), `isGone()` is true for the two statuses that take the page out of the search engines. `google_place_id` (string, nullable) is Google's id of the place. Both are written into the seeders by the freshness tool (`11-data-freshness.md`); an open place has no `business_status` key in its seeder and gets the column default. Both columns come from the migration `2026_11_18_000000_add_business_status_to_places` and are in `$fillable`.

| Table | Extra columns |
|---|---|
| `restaurants` | `type` (cuisine, free text), `lunch_price` (yen), `visited`, `visit_date`, `tabelog_link`, `score_lunch` (0-4), `score_place` (0-3), `score_food` (0-3), `score_price` (0-2), `score_date` (0-2) |
| `stores` | `type`, `score` |
| `bars` | `type`, `visited`, `visit_date`, `tabelog_link`, `score`, `score_place`, `score_mingling`, `score_price` |
| `karaokes` | `area`, `visited`, `visit_date`, `score` |

The score scales are documented as tooltips in `restaurants/details.blade.php`.

### Tags

`restaurant_tags`, `store_tags`, `bar_tags`, `karaoke_tags` all have `id`, timestamps, `label` (string, used as the search key), `type` (unsigned int, drives the tag colour in the UI through the `tagTypeNN` CSS class and the sort order of the tag cloud) and `weight` (unsigned int, secondary sort order). Higher `type` and `weight` are listed first.

Pivot tables `restaurant_restaurant_tag`, `store_store_tag`, `bar_bar_tag`, `karaoke_karaoke_tag` hold the two foreign keys (`foreignId()->constrained()->cascadeOnDelete()`) and no primary key or timestamps.

### Other tables

| Table | Purpose |
|---|---|
| `restaurant_types` | Lookup list of cuisine labels, seeded. Not referenced by `restaurants.type`, which is free text. |
| `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` | Created by the skeleton migrations. `cache` is used; `sessions` only with `SESSION_DRIVER=database`. There is no `users` table any more: the admin accounts went away with the admin (November 2026). |
| `migrations` | Laravel bookkeeping. |

## Eloquent relationships

```
Restaurant  belongsToMany RestaurantTag   (tags / tagsCached)
Store       belongsToMany StoreTag
Bar         belongsToMany BarTag
Karaoke     belongsToMany KaraokeTag
RestaurantTag / StoreTag / BarTag / KaraokeTag  belongsToMany back to the place
```

`attachTags([...labels])` on a place looks up existing tag rows by label (`whereIn`) and attaches them; tags must be created first (hence the seeder order).

The `Location` trait adds `fillCoordinatesFromString('lat, lon')`, which parses and saves coordinates (a trailing third value such as a zoom level pasted from a URL is ignored; some seed entries have one), and `fillCoordinatesFromGoogleLink()`, which extracts the map centre from a Google Maps URL (documented as imprecise). The seeders use the string version.

## Seeders

Seeders live in `database/seeders/` (namespace `Database\Seeders`). `DatabaseSeeder` runs, in order: restaurant types, restaurant tags, restaurants, store tags, stores, bar tags, bars, karaoke tags, karaokes, then flushes the cache. Every seeder starts with a `DELETE` on its table, so `php artisan db:seed` is the "reload the data" operation.

## Cache keys

Everything is cached with `rememberForever`. The seeder flushes the cache; any other data change (SQL, tinker) requires `php artisan cache:clear`. Laravel 13 refuses to unserialize cached objects unless their classes are listed in `cache.serializable_classes`; `config/cache.php` lists the eight place/tag models, the Eloquent `Collection` and the `Pivot` class that tags loaded through `belongsToMany` carry (`business_status` is stored as a plain string in the cached models, so the enum needs no entry).

| Key | Content |
|---|---|
| `all_restaurants_tags`, `all_stores_tags`, ... | Tag cloud for the filter page |
| `All-Restaurant`, `All-Store`, ... | Unfiltered results (open places only, like every results key) |
| `<Model>-md5(op + ',' + tags)` | Results for a given tag combination, prefixed with `cost` when sorted by cost and `l<N>-` when limited |
| `l100-All-Restaurant` | The 100 best restaurants used by the suggestion |
| `restaurant-<id>-tags`, `bar-<id>-tags`, ... | Tags of one place, for the details view |
| `sitemap-en-ja` | The sitemap XML; the key names the languages (`config('app.locales')`) so a new one rebuilds it |

Distances are never cached because they depend on the visitor's position.
