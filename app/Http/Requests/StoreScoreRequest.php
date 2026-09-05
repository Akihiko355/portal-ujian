<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'scores' => 'required|array|min:1',
            'scores.*.subject_id' => 'required|exists:subjects,id',
            'scores.*.score' => 'required|integer|min:0|max:100',
        ];
    }
}
