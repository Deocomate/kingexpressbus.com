<?php

namespace App\Http\Requests\Admin;

use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTripBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // route_id is NOT persisted — used only for dependent select validation
            'route_id'   => ['required', 'integer', 'exists:routes,id'],
            'trip_id'    => [
                'required',
                'integer',
                'exists:trips,id',
                // trip must belong to the submitted route
                Rule::exists('trips', 'id')->where('route_id', $this->input('route_id')),
            ],
            'start_date' => ['required', 'date_format:d/m/Y'],
            'end_date'   => ['required', 'date_format:d/m/Y', 'after_or_equal:start_date'],
            'block_type' => ['required', Rule::in(['off_day', 'sold_out'])],
            'note'       => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function attributes(): array
    {
        return [
            'route_id'   => 'tuyến đường',
            'trip_id'    => 'chuyến xe',
            'start_date' => 'từ ngày',
            'end_date'   => 'đến ngày',
            'block_type' => 'loại khóa',
        ];
    }
}
