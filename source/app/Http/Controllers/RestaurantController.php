<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use Excel;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function main()
    {
        // TODO: implement "see more" button
        $restaurants = Restaurant::all();
        return view('restaurants.index', compact('restaurants'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurants = Restaurant::all();
        return view('restaurants.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('restaurants.create');
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
        return view('restaurants.show',compact('restaurant'));
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
        return view('restaurants.edit',compact('restaurant'));
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
        /*
        $restaurant = Restaurant::find($id);
        $restaurant->delete();
        return redirect('/restaurants-list')->with('success', 'The restaurant has been deleted!');
        */

        Restaurant::find($id)->delete();
        return redirect()->route('restaurants.index')
            ->with('success','Restaurant deleted successfully');


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
     * export a file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $restaurants = Restaurant::all();
        Excel::create('restaurants', function($excel) use($restaurants)
        {
            $excel->sheet('ExportFile', function($sheet) use($restaurants)
            {
                $sheet->fromArray($restaurants);
            });
        })->export('xls');
    }

    /**
     * export a file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportType(Request $request, $type)
    {
        $data = Restaurant::get()->toArray();
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
