<?php

namespace App\Http\Requests\User;

use App\Rules\ValidateMobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user->id ?? $this->route('user')?->id ?? $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id,' . $userId],
            'mobile' => ['required', 'string', 'unique:users,mobile,' . $userId, new ValidateMobileNumber()],
            'email' => ['required', 'string', 'max:100', 'email', 'unique:users,email,' . $userId],
            'role' => ['required', 'exists:roles,name'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,JPG,JPEG,PNG', 'max:2048'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'Employee / Staff ID',
            'mobile' => 'Mobile Number',
            'role' => 'Role',
        ];
    }
}
