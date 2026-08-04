<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id'   => ['required', 'integer', 'exists:routes,id'],
            'bus_id'     => ['required', 'integer', 'exists:buses,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'different:start_time'],
            'price'      => ['required', 'integer', 'min:0'],
            'is_active'  => ['boolean'],
            'priority'   => ['required', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'route_id'   => 'tuyến đường',
            'bus_id'     => 'xe',
            'start_time' => 'giờ xuất bến',
            'end_time'   => 'giờ đến',
            'price'      => 'giá vé',
            'priority'   => 'độ ưu tiên',
        ];
    }
}
