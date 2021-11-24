<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
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
            'branch_id' => ['required'],
            'user_id' => ['required'],
            'punch_in' => ['required', 'date'],
            'punch_out' => ['required', 'date', 'after:punch_in'], 
        ];
    }

    public function messages()
    {
        return [
            'punch_out.after' => 'Punch Out must be a date & time after Punch In attendance.',
        ];
    }
}
