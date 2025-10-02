<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user.name' => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($this->user()),
            ],
        ];
    }
}