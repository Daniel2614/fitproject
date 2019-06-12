<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerFormRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|same:confirmation_password',
            'ci' => 'nullable|ecuador:ci|unique:customers,ci',
            'avatar' => 'nullable|image|max:1024',
            'phone' => 'required|max:10',
            'cell_phone' => 'required|max:10',
            'address' => 'required|max:255',
            'birthdate' => 'required|date',
            'gender' => 'required|max:60',
            'routine_id' => 'required|exists:routines,id',
            'level_id' => 'required|exists:levels,id',
        ];
    }

    public function userRequestParams()
    {
        return [
            'name' => $this->get('name'),
            'last_name' => $this->get('last_name'),
            'avatar' => $this->hasFile('avatar') ? $this->file('avatar')->store('avatars', 'public') : 'avatars/default-avatar.jpg',
            'email' => $this->get('email'),
            'password' => bcrypt($this->get('password')),
            'phone' => $this->get('phone'),
            'cell_phone' => $this->get('cell_phone'),
            'address' => $this->get('address'),
            'role' => 'customer',
        ];
    }

    public function customerRequestParams()
    {
        return [
            'ci' => $this->get('ci'),
            'birthdate' => $this->get('birthdate'),
            'gender' => $this->get('gender'),
            'medical_observations' => $this->get('medical_observations'),
            'routine_id' => $this->get('routine_id'),
            'level_id' => $this->get('level_id'),
        ];
    }
}
