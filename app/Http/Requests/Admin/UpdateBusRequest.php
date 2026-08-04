<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'model_name'      => ['nullable', 'string', 'max:255'],
            'seat_count'      => ['required', 'integer', 'min:1'],
            'services'        => ['nullable', 'array'],
            'services.*'      => ['integer', 'exists:bus_services,id'],
            'priority'        => ['nullable', 'integer'],
            'thumbnail_url'   => ['nullable', 'string'],
            'image_list_url'  => ['nullable', 'array', 'max:10'],
            'image_list_url.*'=> ['string'],
            'content'         => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Vui lòng nhập tên xe.',
            'seat_count.required' => 'Vui lòng nhập số ghế.',
            'seat_count.min'      => 'Số ghế phải ít nhất là 1.',
            'image_list_url.max'  => 'Album tối đa 10 ảnh.',
        ];
    }
}
