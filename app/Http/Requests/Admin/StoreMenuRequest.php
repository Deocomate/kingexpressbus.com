<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use App\Support\Admin\MenuTreeBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'string', Rule::in(['custom_link', 'route', 'page', 'system_page'])],
            'url' => [
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
            'parent_id' => ['required', 'integer'],
            'priority' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $parentId = (int) $this->input('parent_id', Menu::ROOT_PARENT_ID);
            $error = MenuTreeBuilder::parentValidationError($parentId, null);
            if ($error) {
                $validator->errors()->add('parent_id', $error);
            }
        });
    }

    protected function prepareForValidation(): void
    {
        // Ensure root menus always use ROOT_PARENT_ID, not null or 0
        $parentId = $this->input('parent_id');
        if ($parentId === null || $parentId === '' || (int) $parentId === 0) {
            $this->merge(['parent_id' => Menu::ROOT_PARENT_ID]);
        }
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Loại menu không hợp lệ. Chỉ chấp nhận: custom_link, route, page, system_page.',
            'related_id.exists' => 'Tuyến đường được chọn không tồn tại.',
        ];
    }
}
