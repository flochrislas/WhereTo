<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validation of the admin tag form, shared by store() and update() */
class RestaurantTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'type' => 'nullable|integer',
            'weight' => 'nullable|integer',
        ];
    }
}
