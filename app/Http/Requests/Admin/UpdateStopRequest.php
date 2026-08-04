<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'name'        => ['required', 'string', 'max:1000'],
            'address'     => ['required', 'string', 'max:1000'],
            'priority'    => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'district_id.required' => 'Vui lòng chọn địa điểm.',
            'district_id.exists'   => 'Địa điểm không tồn tại.',
            'name.required'        => 'Vui lòng nhập tên điểm dừng.',
            'address.required'     => 'Vui lòng nhập địa chỉ.',
            'priority.required'    => 'Vui lòng nhập độ ưu tiên.',
        ];
    }
}
