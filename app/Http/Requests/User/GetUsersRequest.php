<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class GetUsersRequest extends BaseRequest
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
      'keyword' => 'nullable|string|max:255',
      'role' => 'nullable|string|exists:roles,name',
      'roles' => 'nullable|array',
      'roles.*' => 'string|exists:roles,name',
      'status' => 'nullable|integer|in:0,1',
      'sort_by' => 'nullable|string|in:name,email,user_code,created_at,updated_at',
      'sort_dir' => 'nullable|string|in:asc,desc',
      'limit' => 'nullable|integer|min:1|max:100',
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
      'role.exists' => 'Vai trò không tồn tại.',
      'roles.*.exists' => 'Một hoặc nhiều vai trò không tồn tại.',
      'status.in' => 'Trạng thái không hợp lệ.',
      'sort_by.in' => 'Trường sắp xếp không hợp lệ.',
      'sort_dir.in' => 'Hướng sắp xếp phải là asc hoặc desc.',
      'limit.min' => 'Số lượng mỗi trang phải ít nhất là 1.',
      'limit.max' => 'Số lượng mỗi trang không được vượt quá 100.',
    ];
  }
}
