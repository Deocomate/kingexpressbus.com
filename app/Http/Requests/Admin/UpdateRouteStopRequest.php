<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRouteStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stop_id'   => ['required', 'integer', 'exists:stops,id'],
            'stop_type' => ['required', 'in:pickup,dropoff,both'],
        ];
    }

    public function messages(): array
    {
        return [
            'stop_id.required'   => 'Vui lòng chọn điểm dừng.',
            'stop_id.exists'     => 'Điểm dừng không tồn tại.',
            'stop_type.required' => 'Vui lòng chọn loại điểm dừng.',
            'stop_type.in'       => 'Loại điểm dừng không hợp lệ.',
        ];
    }
}
