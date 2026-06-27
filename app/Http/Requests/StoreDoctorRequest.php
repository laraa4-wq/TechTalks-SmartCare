<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('doctors', 'email')],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^(\+961\s?|0)?\d{1,2}\s?\d{3}\s?\d{3,4}$/'],           
            'specialization_id' => ['nullable', 'integer', Rule::exists('specializations', 'id')],
            'qualification' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }
}