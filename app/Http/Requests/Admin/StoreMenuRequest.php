<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use App\Models\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:1000'],
            'type'       => ['required', 'string', Rule::in(['custom_link', 'route', 'page', 'system_page'])],
            'url'        => [
                Rule::requiredIf(fn () => in_array($this->input('type'), ['custom_link', 'page', 'system_page'])),
                'nullable',
                'string',
                'max:1000',
            ],
            'related_id' => [
                Rule::requiredIf(fn () => $this->input('type') === 'route'),
                'nullable',
                'integer',
                Rule::exists('routes', 'id'),
            ],
            'parent_id'  => ['required', 'integer'],
            'priority'   => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure root menus always use ROOT_PARENT_ID, not null or 0
        $parentId = $this->input('parent_id');
        if (empty($parentId) || (int) $parentId === 0) {
            $this->merge(['parent_id' => Menu::ROOT_PARENT_ID]);
        }
    }

    public function messages(): array
    {
        return [
            'type.in'          => 'Loại menu không hợp lệ. Chỉ chấp nhận: custom_link, route, page, system_page.',
            'related_id.exists' => 'Tuyến đường được chọn không tồn tại.',
        ];
    }
}
