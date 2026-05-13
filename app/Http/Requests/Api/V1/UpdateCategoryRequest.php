<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($id)->whereNull('deleted_at')],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($id)->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
