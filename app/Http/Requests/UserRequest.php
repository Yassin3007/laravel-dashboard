<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name_en' => 'required|max:255',
            'name_ar' => 'required|max:255',
            'is_active' => 'required',
            'company_id' => 'required|exists:companies,id',
            'team_id' => 'required|exists:teams,id',
//            'image' => 'nullable',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
        ];

        // Check if this is an update request
        if ($this->isUpdating()) {
            // For update: password is nullable, but if provided must be confirmed
            $rules['password'] = 'nullable|confirmed';
            // Update email validation to exclude current user
            $userId = $this->route('user') ? $this->route('user')->id : $this->route()->parameter('user');
            $rules['email'] = 'required|email|unique:users,email,' . $userId;
        } else {
            // For store: password is required and confirmed
            $rules['password'] = 'required|confirmed';
        }

        return $rules;
    }

    /**
     * Determine if this is an update request
     *
     * @return bool
     */
    private function isUpdating()
    {
        return $this->isMethod('put') ||
            $this->isMethod('patch') ||
            $this->route('user') !== null;
    }
}
