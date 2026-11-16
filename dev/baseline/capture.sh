#!/bin/sh
# Captures reference HTML from a running WhereTo instance into the directory given as $1.
# Run inside the app container: podman compose exec -T app sh -s /tmp/baseline < dev/baseline/capture.sh
# Starts a second server on port 8001 with the Debugbar disabled so fragments are clean.
# Uses PHP's built-in server directly: "artisan serve" only forwards a whitelist of env vars.
set -eu
out="$1"; mkdir -p "$out"
cd /app
DEBUGBAR_ENABLED=false APP_DEBUG=false php -S 127.0.0.1:8001 -t public public/index.php >/dev/null 2>&1 &
pid=$!
sleep 3
P='35.6586,139.7016'
# shellcheck disable=SC2016
fetch() { # name url
  php -r '$c=stream_context_create(["http"=>["ignore_errors"=>true]]); $b=file_get_contents("http://127.0.0.1:8001".$argv[1],false,$c); echo (function_exists("http_get_last_response_headers") ? http_get_last_response_headers() : $http_response_header)[0]."\n"; file_put_contents($argv[2],$b);' "$2" "$out/$1.html" | sed "s|^|$1: |"
}
fetch home "/"
fetch restaurants-page "/restaurants"
fetch stores-page "/stores"
fetch bars-page "/bars"
fetch karaokes-page "/karaokes"
fetch restaurants-all-ratings "/restaurants/results?orderBy=ratings&position=null"
fetch restaurants-ramen-distance "/restaurants/results?op=AND&tags=ramen&orderBy=distance&position=$P"
fetch restaurants-ramen-japanese-cost "/restaurants/results?op=AND&tags=ramen,japanese&orderBy=cost&position=$P"
fetch stores-all-ratings "/stores/results?orderBy=ratings&position=null"
fetch stores-glasses-distance "/stores/results?op=AND&tags=glasses&orderBy=distance&position=$P"
fetch stores-vinyl-records-cost "/stores/results?op=AND&tags=vinyl,records&orderBy=cost&position=$P"
fetch bars-all-ratings "/bars/results?orderBy=ratings&position=null"
fetch bars-pub-distance "/bars/results?op=AND&tags=pub&orderBy=distance&position=$P"
fetch bars-notablecharge-music-cost "/bars/results?op=AND&tags=notablecharge,music&orderBy=cost&position=$P"
fetch karaokes-all-ratings "/karaokes/results?orderBy=ratings&position=null"
fetch karaokes-joysound-distance "/karaokes/results?op=AND&tags=JOYSOUND&orderBy=distance&position=$P"
fetch karaokes-privaterooms-dam-cost "/karaokes/results?op=AND&tags=privaterooms,DAM&orderBy=cost&position=$P"
fetch restaurant-details-fragment "/restaurants/details/1"
fetch restaurant-details-single "/restaurant/details/1"
fetch store-details-fragment "/stores/details/1"
fetch bar-details-fragment "/bars/details/1"
fetch karaoke-details-fragment "/karaokes/details/1"
fetch suggestion "/suggestions?position=$P"
kill $pid
