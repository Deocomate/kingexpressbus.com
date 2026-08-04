<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHolidaySurchargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                     => ['required', 'string', 'max:255'],
            'reason'                   => ['nullable', 'string'],
            'start_date'               => ['required', 'date_format:d/m/Y'],
            'end_date'                 => ['required', 'date_format:d/m/Y', 'after_or_equal:start_date'],
            'global_surcharge_amount'  => ['required', 'integer', 'min:0'],
            'is_active'                => ['boolean'],
            'priority'                 => ['required', 'integer'],
            'route_adjustments'        => ['nullable', 'array'],
            'route_adjustments.*.route_id'               => ['required', 'integer', 'exists:routes,id'],
            'route_adjustments.*.route_surcharge_amount' => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                    => 'tên phụ thu',
            'start_date'              => 'ngày bắt đầu',
            'end_date'                => 'ngày kết thúc',
            'global_surcharge_amount' => 'phụ thu chung',
            'priority'                => 'độ ưu tiên',
        ];
    }
}
