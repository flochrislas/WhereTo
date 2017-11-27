<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'location',
        'coord',
        'type',
        'lunch_price',
        'points',
        'experience',
        'visited',
        'visit_date'
    ];
    
    // Confirm useless before removing:
    /*
    // From CRUD tuto https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/
    public function saveRestaurant($data)
    {
        // $this->user_id = auth()->user()->id;
        $this->name = $data['name'];
        $this->location = $data['location'];
        $this->coord = $data['coord'];
        $this->type = $data['type'];
        $this->lunch_price = $data['lunch_price'];
        $this->points = $data['points'];
        $this->experience = $data['experience'];
        $this->visited = $data['visited'];
        $this->visit_date = $data['visit_date'];
        $this->save();
        return 1;
    }
    
    // From CRUD tuto https://appdividend.com/2017/10/15/laravel-5-5-crud-example-tutorial/
    public function updateTicket($data)
    {
        $ticket = $this->find($data['id']);
        // $ticket->user_id = auth()->user()->id;
        $this->name = $data['name'];
        $this->location = $data['location'];
        $this->coord = $data['coord'];
        $this->type = $data['type'];
        $this->lunch_price = $data['lunch_price'];
        $this->points = $data['points'];
        $this->experience = $data['experience'];
        $this->visited = $data['visited'];
        $this->visit_date = $data['visit_date'];
        $ticket->save();
        return 1;
    }
    */
    
}
