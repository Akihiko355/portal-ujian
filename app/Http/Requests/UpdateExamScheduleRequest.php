<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'department_id' => 'required|exists:departments,id',
            'briefing_datetime' => 'required|date',
            'exam_start_datetime' => 'required|date|after:briefing_datetime',
            'exam_end_datetime' => 'required|date|after:exam_start_datetime',
            'exam_link' => 'nullable|url',
            'exam_password' => 'required|string|max:255',
            'exam_number' => 'required|string|max:50',
            'link_reveal' => 'required|in:on_briefing,on_start,always',
            'password_reveal' => 'required|in:on_briefing,5_min_before,on_start,always',
            'is_published' => 'boolean',
        ];
    }
}
