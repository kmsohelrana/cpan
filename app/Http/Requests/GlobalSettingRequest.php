<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GlobalSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'system_sms_enabled'     => ['nullable', 'boolean'],
            'system_push_enabled'    => ['nullable', 'boolean'],
            'system_email_enabled'   => ['nullable', 'boolean'],
            'sms_template_body'      => ['nullable', 'string', 'max:1000'],
            'push_template_title'    => ['nullable', 'string', 'max:255'],
            'push_template_body'     => ['nullable', 'string', 'max:1000'],
            'email_template_subject' => ['nullable', 'string', 'max:255'],
            'email_template_body'    => ['nullable', 'string', 'max:2000'],
        ];
    }
}
