<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password; // Import để sử dụng các quy tắc mật khẩu mạnh hơn

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Bất kỳ ai cũng có thể gửi yêu cầu đăng ký
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // email phải là duy nhất
            'password' => [
                'required',
                Password::min(8) // Mật khẩu tối thiểu 8 ký tự
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'email.required' => 'Địa chỉ email là bắt buộc.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ];
    }
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Tạo phản hồi JSON tùy chỉnh
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu đăng ký không hợp lệ.', // Thông báo chung cho lỗi xác thực
            'errors' => $validator->errors() // Chi tiết các lỗi theo trường
        ], 422)); // Mã trạng thái HTTP 422 Unprocessable Entity
    }
}
