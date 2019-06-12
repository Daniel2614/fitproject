<?php

namespace App\Http\Requests;

use App\VitalGym\Entities\Membership;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipFormRequest extends FormRequest
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
            'date_start' => $this->getStartDateRules(),
            'date_end' => 'required|date|after_or_equal:date_start',
            'total_days' => $this->getTotalDaysRules(),
            'customer_id' => 'required|exists:customers,id',
            'membership_quantity' => 'required|integer|min:1',
        ];
    }

    private function getMembershipByRouteParam()
    {
        return optional(Membership::with('plan')->findOrFail($this->route('membershipId')));
    }

    private function getTotalDaysRules(): string
    {
        return (bool) $this->getMembershipByRouteParam()->plan->is_premium
            ? 'required|integer|min:1'
            : '';
    }

    private function getStartDateRules(): string
    {
        return 'required|date|after_or_equal:'.$this->getMembershipByRouteParam()->date_start->toDateString();
    }
}
