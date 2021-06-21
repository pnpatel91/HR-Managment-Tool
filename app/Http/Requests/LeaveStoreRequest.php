<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveStoreRequest extends FormRequest
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
            'employee_id' => ['required'],
            'branch_id' => ['required'],
            'approved_by' => ['required'],
            'leave_type' => ['required'],
            'leave_date' => ['required'],
            'days' => ['required'],
            'half_day' => ['required'],
            'reason' => ['required'],
        ];
    }
}
