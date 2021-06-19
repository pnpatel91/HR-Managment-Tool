<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Holiday extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'holiday_date', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    /**
     * Get the creator of this holiday.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this holiday.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * Get the next holiday.
     */
    public function next_holiday(){
        return $this->where('holiday_date', '>=', Carbon::now())->orderBy('holiday_date')->pluck('id')->first();
    }


    /**
     * The branches that belong to the holiday.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'holidays_has_branches');
    }
    
}
