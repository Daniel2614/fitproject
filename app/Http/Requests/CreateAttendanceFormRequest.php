<?php

namespace App\Http\Requests;

use App\Rules\HasMembershipRule;
use App\Rules\dailyAttendanceRule;
use App\Rules\ActiveMembershipRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceFormRequest extends FormRequest
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
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'before_or_equal:'.now(),
            ],
            'customer_id' => [
                'bail',
                'required',
                'exists:customers,id',
                new dailyAttendanceRule,
                new HasMembershipRule,
                new ActiveMembershipRule,
            ],
        ];
    }
}
