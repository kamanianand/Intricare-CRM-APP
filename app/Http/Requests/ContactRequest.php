<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
        $contactId = $this->route('contact') ? $this->route('contact')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contactId,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
            'additional_file' => 'nullable|file|max:5120',
        ];
    }
}
