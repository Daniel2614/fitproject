<?php

namespace App\Http\Requests;

use App\VitalGym\Entities\Routine;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoutineFormRequest extends FormRequest
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
            'name'  =>  'required|min:3|max:20',
            'file'  =>   'nullable|max:2048|mimes:jpeg,png,jpg,doc,docx,pdf,xlsx',
            'description'   =>  'required|max:255',
            'level_id'  =>  'required|exists:levels,id',
        ];
    }

    public function routineParams()
    {
        $requestedData = collect([
            'level_id' => $this->get('level_id'),
            'name' => $this->get('name'),
            'description' => $this->get('description'),
        ]);

        if ($this->has('file')) {
            Storage::delete($this->getRoutineFileByRouteParam());

            return $requestedData->merge(['file' => $this->file('file')->store('files')])->toArray();
        }

        return $requestedData->toArray();
    }

    public function getRoutineFileByRouteParam()
    {
        return optional(Routine::findOrFail($this->route('routineId')))->file;
    }
}
