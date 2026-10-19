<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$userId}",
            'password' => 'nullable|string|min:6',
            'rfid_uid' => "nullable|string|unique:users,rfid_uid,{$userId}",
            'employment_type' => 'required|in:KARTAP,PKWT,INTERN',
            'basic_salary' => 'required|numeric|min:0',
            'can_approve_leaves' => 'boolean',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:255',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ];
    }
}
