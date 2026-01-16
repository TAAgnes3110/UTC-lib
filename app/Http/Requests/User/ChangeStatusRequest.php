<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class ChangeStatusRequest extends BaseRequest
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
      'status' => 'required|integer|in:0,1',
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
      'status.required' => 'Trạng thái là bắt buộc.',
      'status.in' => 'Trạng thái phải là 0 (khóa) hoặc 1 (mở khóa).',
    ];
  }
}
