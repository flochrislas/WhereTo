How about a nice {{ strtolower($place->type ?? '') }} restaurant, <a href="{{ route('restaurants.details.single', $place->id) }}">{{ $place->name }}</a>?
