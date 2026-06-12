<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name' => 'required|string|max:191',
            'username' => ['required', 'string', 'max:191', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,petugas,viewer',
        ];
    }
}
