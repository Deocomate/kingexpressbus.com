<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_start_id'     => ['required', 'integer', 'exists:provinces,id'],
            'province_end_id'       => ['required', 'integer', 'exists:provinces,id'],
            'name'                  => ['required', 'string', 'max:255'],
            'slug'                  => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:routes,slug'],
            'title'                 => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'duration'              => ['nullable', 'string', 'max:50'],
            'distance_km'           => ['nullable', 'integer', 'min:0'],
            'price_default'         => ['nullable', 'integer', 'min:0'],
            'available_hotel_pickup'=> ['boolean'],
            'priority'              => ['nullable', 'integer'],
            'thumbnail_url'         => ['nullable', 'string'],
            'image_list_url'        => ['nullable', 'array'],
            'image_list_url.*'      => ['string'],
            'content'               => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'province_start_id.required' => 'Vui lòng chọn tỉnh đầu.',
            'province_end_id.required'   => 'Vui lòng chọn tỉnh cuối.',
            'name.required'              => 'Vui lòng nhập tên tuyến.',
            'slug.required'              => 'Slug không được để trống.',
            'slug.unique'                => 'Slug đã tồn tại.',
            'slug.regex'                 => 'Slug chỉ chứa chữ thường, số và dấu gạch ngang.',
        ];
    }
}
