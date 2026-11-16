<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validation of the admin restaurant type form, shared by store() and update() */
class RestaurantTypeRequest extends FormRequest
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
        ];
    }
}
