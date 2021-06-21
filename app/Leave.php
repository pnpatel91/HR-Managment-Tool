<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id', 'approved_by', 'branch_id', 'start_at', 'end_at', 'leave_days', 'leave_type', 'reason', 'description', 'half_day', 'status', 'created_at', 'updated_at'
    ];

    /**
     * Get the creator employee this leave.
     */
    public function employee(){
        return $this->belongsTo(User::class,'employee_id');
    }

    /**
     * Get this the leave by the approved.
     */
    public function leave_approved_by(){
        return $this->belongsTo(User::class,'approved_by');
    }

    /**
     * Get this the branch by the approved.
     */
    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id')->with('company');
    }

}
