<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\VitalGym\Entities\Customer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerFormRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->getUserByRouteParam()->id),
            ],
            'password' => 'nullable|min:6|max:64|same:password_confirmation',
            'ci' => [
                'nullable',
                'ecuador:ci',
                Rule::unique('customers')->ignore($this->getCustomerByRouteParam()->id),
            ],
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
        $userRequestData = collect([
            'name' => $this->get('name'),
            'last_name' => $this->get('last_name'),
            'avatar' => $this->hasFile('avatar') ? $this->file('avatar')->store('avatars', 'public') : $this->getUserByRouteParam()->avatar,
            'email' => $this->get('email'),
            'phone' => $this->get('phone'),
            'cell_phone' => $this->get('cell_phone'),
            'address' => $this->get('address'),
        ]);

        if ($this->get('password') !== null) {
            return $userRequestData->merge(['password' => bcrypt($this->get('password'))])->toArray();
        }

        return $userRequestData->toArray();
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

    public function getUserByRouteParam()
    {
        return optional(Customer::with('user')->findOrFail($this->route('customerId')))->user;
    }

    public function getCustomerByRouteParam()
    {
        return optional(Customer::with('user')->findOrFail($this->route('customerId')));
    }
}
