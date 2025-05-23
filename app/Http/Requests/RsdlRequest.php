<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Purifier\Facades\Purifier;

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
            'rsdl_order' => 'nullable|integer',
            'display' => 'required|boolean',
            'ref_id' => 'nullable|integer',
            'active' => 'required|boolean',
            'rsdl_img' => 'nullable|integer|exists:tbimage,image_id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('rsdl_detail')) {
            $this->merge([
                'rsdl_detail' => Purifier::clean($this->input('rsdl_detail')),
            ]);
        }
    }
}
