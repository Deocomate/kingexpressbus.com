<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreWebProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_name'         => ['required', 'string', 'max:1000'],
            'is_default'           => ['boolean'],
            'title'                => ['nullable', 'string', 'max:1000'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'logo_url'             => ['nullable', 'string', 'max:2000'],
            'favicon_url'          => ['nullable', 'string', 'max:2000'],
            'email'                => ['nullable', 'email', 'max:1000'],
            'phone'                => ['nullable', 'string', 'max:1000'],
            'hotline'              => ['nullable', 'string', 'max:1000'],
            'whatsapp'             => ['nullable', 'string', 'max:1000'],
            'address'              => ['nullable', 'string', 'max:1000'],
            'facebook_url'         => ['nullable', 'url', 'max:1000'],
            'zalo_url'             => ['nullable', 'url', 'max:1000'],
            'map_embedded'         => ['nullable', 'string'],
            'policy_content'       => ['nullable', 'string'],
            'introduction_content' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
        ]);
    }
}
