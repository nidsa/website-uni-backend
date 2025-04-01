<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RsdlRequest extends FormRequest
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
            'rsdl_title' => 'nullable|string|max:255',
            'rsdl_detail' => 'nullable|string',
            'rsdl_fav' => 'required|boolean',
            'lang' => 'nullable|integer',
            'rsdl_order' => 'required|integer',
            'display' => 'required|boolean',
            'active' => 'required|boolean',
        ];
    }
}
