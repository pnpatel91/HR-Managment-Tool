<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RotaStoreByRotaTemplateRequest extends FormRequest
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
            'start_date' => 'required|date_format:m/d/Y|after_or_equal:today',
            'end_date' => 'required|date_format:m/d/Y|after_or_equal:start_date',
            'start_at' => 'required',
            'end_at' => 'required',
            'max_start_at' => 'required|after_or_equal:start_at',
            'break_time' => 'required|numeric|min:0|max:120',
            'types' => 'required',
            'over_time' => 'required',
        ];
    }
}
