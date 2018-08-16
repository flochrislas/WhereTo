<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Restaurant;
use App\Tools\GeoUtils;
use Excel;

class RestaurantController extends Controller
{

    public function main(Request $request)
    {
      // main became for filters, tags, not restaurants
      return view('restaurants.main');
    }

    /**
     * Public
     * Search for the list to display.
     * Cache.
     * TODO: implement "see more" button
     *
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        // Reads request parameters
        Log::debug('Request tags: '.request('tags'));
        Log::debug('Request op: '.request('op'));
        Log::debug('Request position: '.request('position'));
        Log::debug('Request orderBy: '.request('orderBy'));
        // Operator for the Tags
        $op = request('op');
        // tags as a comma separated list
        $tags = request('tags');
        $this->formatTags($tags);
        // Current position from client's GPS
        // Office from google maps '35.656660, 139.699691'
        $position = request('position');
        $position = '35.656660, 139.699691';

        $orderBy = request('orderBy');

        // key needs to be made of all the used parameters except coords
        $cacheKey = $this->cacheKey($op, $tags, $orderBy);

        $restaurants = Cache::rememberForever($cacheKey, function() use ($tags, $op, $orderBy) {
              return $this->mainDB($tags, $op, $orderBy);
          });

        if ($orderBy == 'distance')
        {
          if (!empty($position)) {
              $distanceSortedRestaurants = $this->sortByDistance($restaurants, $position);
          }
          $restaurants = collect($distanceSortedRestaurants);
        }
        else // we still need to fill the current distance data for each restaurant we show
        {
          if (!empty($position)) {
              $distanceSortedRestaurants = $this->generateCurrentDistances($restaurants, $position);
          }
        }

        // Keeps the input of the user interface
        // https://laravel.com/docs/5.5/requests#old-input
        $request->flash();

        // return view('restaurants.main', compact('restaurants'));
        return view('restaurants.results-data', compact('restaurants'));
    }

    /**
     * Generate the key to use for puting the main query into cache
     * @return string
     */
    public function cacheKey($op, $tags, $orderBy) : string
    {
        if (empty($tags)) {
            $cacheKey = 'all';
        } else {
            $cacheKey = md5($op .','. implode(',',$tags));
        }
        // The logic below is a bit dangerous, but currently
        // only orderBy costs influence the cached results
        if ($orderBy == 'cost') {
            $cacheKey = $orderBy.$cacheKey;
        }
        return $cacheKey;
    }

    /**
     * Make the tags into an array, if it is not already the case
     * The reference is passed in paramter, so the function directly modifies the tags
     * @return void
     */
    public function formatTags(&$tags)
    {
        if (empty($tags)) {
            $tags = null;
        } elseif (!($tags instanceof Traversable)) {
            Log::debug('tags is being transformed from: '.$tags);
            $tags = explode(',',str_replace(', ', ',', $tags));
            Log::debug('to this: '.print_r($tags, true));
        }
    }

    /**
     * Search for the list to display.
     * No cache, just the DB.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function mainDB($tags, $op = 'AND', $orderBy = 'distance')
    {
        Log::debug('Hitting the DB with mainDB with op '.$op.' orderBy '.$orderBy);
        $query = (new Restaurant)->newQuery();

        $this->whereTags($query, $tags, $op);
        $this->orderBy($query, $orderBy);

        $restaurants = $query->get();

        return $restaurants;
    }

    public function orderBy(&$query, $orderBy = 'distance') : void
    {
        Log::debug('orderBy: '.$orderBy);

        if ($orderBy == 'distance' || $orderBy == 'ratings') {
            // Sorting by distance will happens outside the query
            // We still try to put best places on top (rating)
            $query->orderBy('score_lunch', 'desc')
                  ->orderBy('score_food', 'desc')
                  ->orderBy('score_place', 'desc');
        }

        if ($orderBy == 'cost') {
            // Price first, but we still try to put best places on top
            $query->orderBy('lunch_price', 'asc')
                  ->orderBy('score_lunch', 'desc')
                  ->orderBy('score_food', 'desc')
                  ->orderBy('score_place', 'desc');
        }
    }

    public function whereTags(&$query, $tags, $op = 'AND') : void
    {
        Log::debug('whereTags: '.print_r($tags, true).'('.$op.')');
        if (isset($tags) && !empty($tags)) {
            if ($op == 'AND') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                }, '=', count($tags));
            }

            if ($op == 'OR') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                });
            }

            if ($op == 'ANDNOT') {
                $query->whereDoesntHave('tags', function ($query) use ($tags) {
                      $query->whereIn('label', $tags);
                }, '=', count($tags));
            }

            if ($op == 'ORNOT') {
                $query->whereHas('tags', function ($query) use ($tags) {
                      $query->whereNotIn('label', $tags);
                });
            }
        }
    }

    public function whereTypes(&$query, $types, $op = 'AND') : void
    {
        if (isset($types) && !empty($types)) {

            if ($op == 'AND') {
                $query->whereHas('tags', function ($query) use ($types) {
                    foreach ($types as $type) {
                      $query->where('label', '=', $type);
                    }
                });
            }

            if ($op == 'OR') {
                $query->whereHas('tags', function ($query) use ($types) {
                      $query->whereIn('label', $types);
                });
            }

            if ($op == 'ANDNOT') {
                $query->whereDoesntHave('tags', function ($query) use ($types) {
                    foreach ($types as $type) {
                      $query->where('label', '=', $type);
                    }
                });
            }

            if ($op == 'ORNOT') {
                $query->whereHas('tags', function ($query) use ($types) {
                      $query->whereNotIn('label', $types);
                });
            }
        }
    }

    /**
     * Return the restaurants sorted by distance from given position
     * @return array
     */
    public function sortByDistance($restaurants, $position)
    {
        Log::debug('sortByDistance from  '.$position.' for '.count($restaurants).' restaurants' );
        // Log::debug('For restaurants '.print_r($restaurants, true));
        // Map the restaurants with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        $map = array();
        foreach ($restaurants as $resto) {
          if(!is_null($resto->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $resto->lat, $resto->lon);
            // Log::debug('Distance found: '.$distance);
            $resto->currentDistance = $distance;
            /*
            if (array_key_exists((string)$distance, $map)) {
                Log::debug('UH OH, this distance is already there '.$distance.', byebye '.$map[$distance]);
            }
            if (isset($map[$distance]))
            {
              Log::debug('OOOOOOH, this distance is already there '.$distance.', byebye '.$map[$distance]);
            }
            */
            // We must use a string or else the number will be truncated
            // and easily collide with previous keys
            $distanceAsString = (string)$distance;
            $map[$distanceAsString] = $resto;
          }
          else {
            Log::debug('The following restaurant does NOT have coordinates: '.$resto->name);
          }
        }
        // Sort the map by key
        ksort($map);
        // Log::debug('sortByDistance ksort the map '.print_r($map, true));
        Log::debug('sortByDistance AFTER ksort the map with '.count($map).' elements');
        // Return just the list of restaurants
        return array_values($map);
    }

    /**
     * For all the given restaurants, compute the distance from the given position
     * Each result is stored in the restaurant's model
     * @return void
     */
    public function generateCurrentDistances($restaurants, $position)
    {
        Log::debug('generateCurrentDistances from  '.$position);
        // Map the restaurants with their current distance
        $curCoords = GeoUtils::toPositionArray($position);
        $curLat = $curCoords[0];
        $curLon = $curCoords[1];
        foreach ($restaurants as $resto) {
          if(!is_null($resto->lat)) {
            $distance = GeoUtils::distance($curLat, $curLon, $resto->lat, $resto->lon);
            $resto->currentDistance = $distance;
          }
        }
    }

    /**
     * Public
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $restaurant = Restaurant::find($id);
        return view('restaurants.details', compact('restaurant'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurants = Restaurant::orderBy('id', 'desc')->get();
        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // From CRUD tuto https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/
        /*
        $restaurant = new restaurant();
        $data = $this->validate($request, [
            'name'=>'required',
            'type'=> 'required'
        ]);

        $restaurant->saverestaurant($data);
        return redirect('/restaurant-create')
            ->with('success', 'New restaurant has been created! Wait sometime to get resolved');
        */
        ////////////////////////////////////////////////// VS

        request()->validate([
            'name'=>'required',
            'type'=> 'required'
        ]);

        Restaurant::create($request->all());

        return redirect()->route('restaurants.index')
            ->with('success','Restaurant created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        return view('admin.restaurants.show',compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $restaurant = Restaurant::find($id);
        return view('admin.restaurants.edit',compact('restaurant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*
        $ticket = new Ticket();
        $data = $this->validate($request, [
            'name'=>'required',
            'type'=> 'required'
        ]);
        $data['id'] = $id;
        $ticket->updateTicket($data);

        return redirect('/restaurants-list')->with('success', 'The restaurant has been updated!');
        */
        ////////////////////////// VS

        request()->validate([
            'name'=>'required',
            'type'=> 'required'
        ]);

        Restaurant::find($id)->update($request->all());

        return redirect()->route('restaurants.index')
            ->with('success','Restaurant updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Restaurant::find($id)->delete();
        return redirect()->route('restaurants.index')
            ->with('success','Restaurant deleted successfully');
    }

    /**
     * export a file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $type)
    {
        $data = Restaurant::all();
        return Excel::create('restaurants-export', function($excel) use ($data)
        {
            $excel->sheet('Restaurants Export', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if($request->file('imported-file'))
        {
            $path = $request->file('imported-file')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();

            if(!empty($data) && $data->count())
            {
                $data = $data->toArray();
                for($i=0;$i<count($data);$i++)
                {
                    $dataImported[] = $data[$i];
                }
            }
            Restaurant::insert($dataImported);
            return back()->with('success','Restaurants import successful.');
        }
        return back()->with('error','Something seems to be wrong with the import file.');;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importAlt(Request $request)
    {
        if($request->hasFile('imported-file'))
        {
            $path = $request->file('imported-file')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();

            if (!empty($data) && $data->count())
            {
                foreach ($data->toArray() as $key => $value)
                {
                    if (!empty($value))
                    {
                        foreach ($value as $v)
                        {
                            $insert[] = [
                                'name' => $v['name'],
                                'location' => $v['location'],
                                'type' => $v['type'],
                                'lunch_price' => $v['lunch_price'],
                                'points' => $v['points'],
                                'experience' => $v['experience'],
                                'visited' => $v['visited'],
                                'visit_time' => $v['visit_time']
                            ];
                        }
                    }
                }

                if (!empty($insert))
                {
                    Robot::insert($insert);
                    return back()->with('success','Restaurants import successful.');
                }
            }
        }
        return back()->with('error','Something seems to be wrong with the import file.');
    }

}
