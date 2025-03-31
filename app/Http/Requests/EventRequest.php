<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'e_title' => 'nullable|string|max:255',
            'e_shorttitle' => 'nullable|string|max:255',
            'e_img' => 'nullable|exists:tbimage,image_id',
            'e_tags' => 'nullable|string|max:50',
            'e_date' => 'nullable|date',
            'e_detail' => 'nullable|string',
            'e_fav' => 'required|boolean',
            'lang' => 'nullable|integer',
            'e_order' => 'required|integer',
            'display' => 'required|boolean',
            'active' => 'required|boolean'
        ];
    }
}
