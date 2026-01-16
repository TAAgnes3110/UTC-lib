<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class StoreUserRequest extends BaseRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    // Có thể thêm logic kiểm tra quyền ở đây
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
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:users,email',
      'password' => 'required|string|min:6',
      'user_code' => 'nullable|string|max:255|unique:users,user_code',
      'status' => 'nullable|integer|in:0,1',
      'role' => 'nullable|string|exists:roles,name',
      'roles' => 'nullable|array',
      'roles.*' => 'string|exists:roles,name',
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
      'name.required' => 'Tên người dùng là bắt buộc.',
      'name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
      'email.required' => 'Email là bắt buộc.',
      'email.email' => 'Email không đúng định dạng.',
      'email.unique' => 'Email đã tồn tại trong hệ thống.',
      'password.required' => 'Mật khẩu là bắt buộc.',
      'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
      'user_code.unique' => 'Mã người dùng đã tồn tại trong hệ thống.',
      'status.in' => 'Trạng thái không hợp lệ.',
      'role.exists' => 'Vai trò không tồn tại.',
      'roles.*.exists' => 'Một hoặc nhiều vai trò không tồn tại.',
    ];
  }
}
