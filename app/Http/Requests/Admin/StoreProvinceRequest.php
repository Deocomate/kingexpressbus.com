<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:1000'],
            'slug'          => ['required', 'string', 'max:1000', Rule::unique('provinces', 'slug')],
            'title'         => ['nullable', 'string', 'max:1000'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'priority'      => ['required', 'integer'],
            'thumbnail_url' => ['nullable', 'string'],
            'image_list_url'=> ['nullable', 'array'],
            'image_list_url.*' => ['nullable', 'string'],
            'content'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Vui lòng nhập tên tỉnh/thành.',
            'slug.required'     => 'Vui lòng nhập đường dẫn.',
            'slug.unique'       => 'Đường dẫn đã tồn tại.',
            'priority.required' => 'Vui lòng nhập độ ưu tiên.',
        ];
    }
}
