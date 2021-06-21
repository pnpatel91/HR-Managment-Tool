<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchUpdateRequest extends FormRequest
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
            'name' => 'required|max:200|string',
            'company_id' => 'required',
            'latitude' => ['required', 'regex:/[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)+/'],
            'longitude' => ['required', 'regex:/[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)+/'],
        ];
    }
}
