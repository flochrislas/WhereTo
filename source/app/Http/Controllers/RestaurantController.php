<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Restaurant;
use App\Tools\GeoUtils;
use Excel;

class RestaurantController extends PlaceController
{
    const RESULTS_DATA_VIEW = 'restaurants.results-data';

    const DETAILS_VIEW = 'restaurants.details';

    const MODEL_CLASS = 'App\Restaurant';

    /**
    * Returns the Model class to use to read the data
    * @return Model's fully specified class name as a string
    */
    public function getModelClass()
    {
        return self::MODEL_CLASS;
    }

    /**
     * Get the list to display.
     * TODO: implement "see more" button
     *
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        // -----------------------------------
        // Reads request parameters

        // Operator for the Tags
        $op = request('op');

        // tags as a comma separated list
        $tags = request('tags');
        // makes tag into a traversable array
        $this->formatTags($tags);

        // Current position from client's GPS
        // Office from google maps '35.656660, 139.699691'
        $position = request('position');
        $position = '35.656660, 139.699691';

        // the sorting order for the result list
        $orderBy = request('orderBy');

        // -----------------------------------
        // Reads from cache or DB
        $places = $this->useCache($tags, $op, $orderBy);

        // -----------------------------------
        // Calculate distances from position, and sort them if required
        $places = $this->handleDistances($places, $position, $orderBy);

        // Keeps the input of the user interface
        // https://laravel.com/docs/5.5/requests#old-input
        $request->flash();

        // Return the view with the resulted places
        return view(self::RESULTS_DATA_VIEW, compact('places'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $place = (self::MODEL_CLASS)::find($id);
        return view(self::DETAILS_VIEW, compact('place'));
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
