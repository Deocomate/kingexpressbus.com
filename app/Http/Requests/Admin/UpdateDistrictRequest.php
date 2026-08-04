<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $district = $this->route('district');

        return [
            'province_id'      => ['required', 'integer', Rule::exists('provinces', 'id')],
            'district_type_id' => ['required', 'integer', Rule::exists('district_types', 'id')],
            'name'             => ['required', 'string', 'max:1000'],
            'slug'             => ['required', 'string', 'max:1000', Rule::unique('districts', 'slug')->ignore($district)],
            'title'            => ['nullable', 'string', 'max:1000'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'priority'         => ['required', 'integer'],
            'thumbnail_url'    => ['nullable', 'string'],
            'image_list_url'   => ['nullable', 'array'],
            'image_list_url.*' => ['nullable', 'string'],
            'content'          => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'province_id.required'      => 'Vui lòng chọn tỉnh/thành.',
            'province_id.exists'        => 'Tỉnh/thành không tồn tại.',
            'district_type_id.required' => 'Vui lòng chọn loại địa điểm.',
            'district_type_id.exists'   => 'Loại địa điểm không tồn tại.',
            'name.required'             => 'Vui lòng nhập tên địa điểm.',
            'slug.required'             => 'Vui lòng nhập đường dẫn.',
            'slug.unique'               => 'Đường dẫn đã tồn tại.',
            'priority.required'         => 'Vui lòng nhập độ ưu tiên.',
        ];
    }
}
