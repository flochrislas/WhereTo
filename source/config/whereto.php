<?php

/*
|--------------------------------------------------------------------------
| WhereTo settings
|--------------------------------------------------------------------------
*/

return [

    /*
    | The stand-alone mode of the views (doc/16-standalone-apk-plan.md): the date of the export, or null on the
    | website. Never set from .env: only "php artisan app:export-static" sets it, at run time, while it writes the
    | pages of the Android app. In that mode the URLs are root-relative, the layout loads whereto-local.js, and the
    | controls that make no sense without a server (Copy URL, the manifest) are not printed (App\Support\Standalone).
    */
    'standalone' => null,

];
