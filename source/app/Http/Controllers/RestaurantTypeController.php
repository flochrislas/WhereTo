<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Restaurant type controller.
 * Using tuto https://www.codexworld.com/laravel-tutorial-crud-add-edit-delete-operations/
 */
class RestaurantTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //fetch all restaurantTypes data
        $restaurantTypes = restaurantType::orderBy('created','desc')->get();
        
        //pass restaurantTypes data to view and load list view
        return view('restaurantType.index', ['restaurantTypes' => $restaurantTypes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //load form view
        return view('restaurantType.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate restaurantType data
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required'
        ]);
        
        //get restaurantType data
        $restaurantTypeData = $request->all();
        
        //insert restaurantType data
        restaurantType::create($restaurantTypeData);
        
        //store status message
        Session::flash('success_msg', 'Restaurant Type added successfully!');
        
        return redirect()->route('restaurantType.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //fetch restaurantType data
        $restaurantType = restaurantType::find($id);
        
        //pass restaurantTypes data to view and load list view
        return view('restaurantType.details', ['restaurantType' => $restaurantType]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //get restaurantType data by id
        $restaurantType = restaurantType::find($id);
        
        //load form view
        return view('restaurantType.edit', ['restaurantType' => $restaurantType]);
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
        //validate restaurantType data
        $this->validate($request, [
            'label' => 'required'
        ]);
        
        //get restaurantType data
        $restaurantTypeData = $request->all();
        
        //update restaurantType data
        restaurantType::find($id)->update($restaurantTypeData);
        
        //store status message
        Session::flash('success_msg', 'Restaurant Type updated successfully!');
        
        return redirect()->route('restaurantType.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //update restaurantType data
        restaurantType::find($id)->delete();
        
        //store status message
        Session::flash('success_msg', 'Restaurant Type deleted successfully!');
        
        return redirect()->route('restaurantType.index');
    }
}
