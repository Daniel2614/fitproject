<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserFormRequest extends FormRequest
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
        return [
            'name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:64|same:password_confirmation',
            'avatar' => 'nullable|image|max:1024',
            'phone' => 'required|max:10',
            'cell_phone' => 'required|max:10',
            'address' => 'required|max:255',
            'active' => 'required|boolean',
        ];
    }

    public function userParams()
    {
        return collect([
            'name' => request()->get('name'),
            'last_name' => request()->get('last_name'),
            'avatar' => request()->hasFile('avatar')
                ? request()->file('avatar')->store('avatars', 'public')
                : 'avatars/default-avatar.jpg',
            'email' => request()->get('email'),
            'phone' => request()->get('phone'),
            'cell_phone' => request()->get('cell_phone'),
            'address' => request()->get('address'),
            'role' => request()->get('role'),
            'active' => request()->get('active'),
            'password' => bcrypt(request()->get('password')),
        ])->toArray();
    }
}
