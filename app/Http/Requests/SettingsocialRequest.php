<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsocialRequest extends FormRequest
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
            'set_id'           => 'required|integer|exists:tbsetting,set_id',
            'set_facultytitle' => 'nullable|string|max:50',
            'set_facultydep'   => 'nullable|string|max:50',
            'set_logo'         => 'nullable|integer|exists:tbimage,image_id',
            'set_amstu'        => 'required|numeric',
            'set_enroll'       => 'required|numeric',
            'lang'             => 'nullable|integer|in:1,2',

            'setting_social' => 'nullable|array',
            'setting_social.*.setsoc_title' => 'nullable|string|max:50',
            'setting_social.*.setsoc_img' => 'nullable|integer|exists:tbimage,image_id',
            'setting_social.*.setsoc_link' => 'nullable|string|max:255',
            'setting_social.*.setsoc_order' => 'nullable|integer',
        ];
    }
}
