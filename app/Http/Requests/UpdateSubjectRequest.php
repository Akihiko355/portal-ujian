<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($this->route('subject'))],
            'credits' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'passing_grade' => $this->passing_grade ?? 70,
        ]);
    }
}
