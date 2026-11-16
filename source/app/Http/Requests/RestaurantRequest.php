<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation of the admin restaurant form, shared by store() and update().
 * A "form request" is validated before the controller method runs: on failure Laravel redirects back
 * to the form with the errors and the old input, and the controller never sees the request.
 */
class RestaurantRequest extends FormRequest
{
    /** Who may send this form: anyone who reached the route (the admin routes already require a logged-in admin) */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The rules, one entry per form field. "nullable" lets a field be left empty.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'lunch_price' => 'nullable|integer|min:0',
            'points' => 'nullable|string',
            'experience' => 'nullable|string',
            'visited' => 'nullable|boolean',
            'visit_date' => 'nullable|date',
            'google_maps_link' => 'nullable|string',
            'tabelog_link' => 'nullable|string|max:255',
            'official_website' => 'nullable|string|max:255',
            'score_lunch' => 'nullable|integer|between:0,4',
            'score_place' => 'nullable|integer|between:0,3',
            'score_food' => 'nullable|integer|between:0,3',
            'score_price' => 'nullable|integer|between:0,2',
            'score_date' => 'nullable|integer|between:0,2',
            // "lat, lon" typed by the admin; parsed by the controller, not a column
            'coord' => 'nullable|string|max:100',
        ];
    }
}
