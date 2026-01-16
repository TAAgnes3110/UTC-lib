<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required_without:user_code|email',
            'user_code' => 'required_without:email|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required_without' => 'Email hoặc mã người dùng là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'user_code.required_without' => 'Mã người dùng hoặc email là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
        ];
    }
}
