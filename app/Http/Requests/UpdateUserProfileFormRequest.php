<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileFormRequest extends FormRequest
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
            'last_name' => 'required|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'password' => 'nullable|min:6|max:64|same:password_confirmation',
            'avatar' => 'nullable|image|max:1024',
            'phone' => 'required|max:10',
            'cell_phone' => 'required|max:10',
            'address' => 'required|max:255',
        ];
    }

    public function userParams()
    {
        $userRequestData = collect([
            'name' => request()->get('name'),
            'last_name' => request()->get('last_name'),
            'avatar' => request()->hasFile('avatar') ? request()->file('avatar')->store('avatars', 'public') : auth()->user()->avatar,
            'email' => request()->get('email'),
            'phone' => request()->get('phone'),
            'cell_phone' => request()->get('cell_phone'),
            'address' => request()->get('address'),
        ]);

        if (request()->get('password') !== null) {
            return $userRequestData->merge(['password' => bcrypt(request()->get('password'))])->toArray();
        }

        return $userRequestData->toArray();
    }
}
