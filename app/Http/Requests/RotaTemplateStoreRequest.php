<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RotaTemplateStoreRequest extends FormRequest
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
            'name' => 'required',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i',
            'max_start_at' => 'required|date_format:H:i|after_or_equal:start_at',
            'break_time' => 'required|numeric|min:0|max:120',
            'types' => 'required',
            'over_time' => 'required',
        ];
    }
}
