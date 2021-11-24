<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time','status', 'distance', 'latitude', 'longitude', 'ip_address', 'branch_id', 'punch_in_id', 'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    /**
     * Get the creator of this attendance.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this attendance.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * The branches that belong to the user.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id')->with('company');
    }

    /**
     * The punch_in that belong to the attendances.
     */
    public function punch_in()
    {
        return $this->belongsTo($this, 'punch_in_id');
    }


    public function punch_out() {
        return $this->hasOne($this, 'punch_in_id');
    }
}
