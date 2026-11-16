# Data model

The schema is defined by the migrations in `source/database/migrations/`. The real content of the app is in the seeders: there is no admin UI for stores, bars or karaoke, and restaurant data edits were committed as "data update" commits to the seeders.

## Tables

### Places

All four place tables share the same core: `id`, `created_at`, `updated_at`, `name`, `location` (a free-text area name such as "Shibuya"), `lat`, `lon` (nullable floats), `points` (short summary shown in the list, truncated to 50 characters), `experience` (long personal review), `google_maps_link`, `official_website`.

| Table | Extra columns |
|---|---|
| `restaurants` | `type` (cuisine, free text), `lunch_price` (yen), `visited`, `visit_date`, `tabelog_link`, `score_lunch` (0-4), `score_place` (0-3), `score_food` (0-3), `score_price` (0-2), `score_date` (0-2) |
| `stores` | `type`, `score` |
| `bars` | `type`, `visited`, `visit_date`, `tabelog_link`, `score`, `score_place`, `score_mingling`, `score_price` |
| `karaokes` | `area`, `visited`, `visit_date`, `score` |

The score scales are documented as tooltips in `restaurants/details.blade.php`.

### Tags

`restaurant_tags`, `store_tags`, `bar_tags`, `karaoke_tags` all have `id`, timestamps, `label` (string, used as the search key), `type` (unsigned int, drives the tag colour in the UI through the `tagTypeNN` CSS class and the sort order of the tag cloud) and `weight` (unsigned int, secondary sort order). Higher `type` and `weight` are listed first.

Pivot tables `restaurant_restaurant_tag`, `store_store_tag`, `bar_bar_tag`, `karaoke_karaoke_tag` hold the two foreign keys with `ON DELETE CASCADE` and no primary key or timestamps.

### Other tables

| Table | Purpose |
|---|---|
| `restaurant_types` | Lookup list of cuisine labels, editable in admin. Not referenced by `restaurants.type`, which is free text. |
| `users` | Laravel users plus a `level` string column (filled with `'1'` by the seeder, never read). |
| `password_resets` | Stock Laravel. |
| `user_restaurant` | Pivot "user visited restaurant" with a `comment` text column and timestamps. Declared on both models, never used by any page. |
| `cache` | Backing store for the `database` cache driver. |
| `migrations` | Laravel bookkeeping. |

## Eloquent relationships

```
Restaurant  belongsToMany RestaurantTag   (tags / tagsCached)
Restaurant  belongsToMany User            (users, pivot comment)
User        belongsToMany Restaurant      (restaurants, pivot comment)
Store       belongsToMany StoreTag
Bar         belongsToMany BarTag
Karaoke     belongsToMany KaraokeTag
RestaurantTag / StoreTag / BarTag / KaraokeTag  belongsToMany back to the place
```

`attachTags([...labels])` on a place looks up existing tag rows by label and attaches them; tags must be created first (hence the seeder order).

The `Location` trait adds `fillCoordinatesFromString('lat, lon')`, which parses and saves coordinates, and `fillCoordinatesFromGoogleLink()`, which extracts the map centre from a Google Maps URL (documented as imprecise). The seeders use the string version.

## Seeders

`DatabaseSeeder` runs, in order: users, restaurant types, restaurant tags, restaurants, store tags, stores, bar tags, bars, karaoke tags, karaokes, then flushes the cache. Every seeder starts with a `DELETE` on its table, so `php artisan db:seed` is the "reload the data" operation.

`UsersTableSeeder` creates two accounts with the password `password`; change them before exposing the admin.

## Cache keys

Everything is cached with `rememberForever`, so any data change requires `php artisan cache:clear` (the seeder does it for you).

| Key | Content |
|---|---|
| `all_restaurants_tags`, `all_stores_tags`, ... | Tag cloud for the filter page |
| `All-Restaurant`, `All-Store`, ... | Unfiltered results |
| `md5(op + ',' + tags)` | Results for a given tag combination, prefixed with `cost` when sorted by cost and `l<N>-` when limited |
| `l100-All-Restaurant` | The 100 best restaurants used by the suggestion |
| `restaurant-<id>-tags`, `bar-<id>-tags`, ... | Tags of one place, for the details view |
| `<term>` | Raw autocomplete term (collides with any other key of the same name) |

Distances are never cached because they depend on the visitor's position.
