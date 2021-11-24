<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date', 'start_time', 'end_date', 'end_time', 'max_start_time', 'break_start_time', 'break_time', 'over_time', 'remotely_work', 'branch_id', 'notes', 'user_id', 'rota_templates_id', 'created_by', 'updated_by'
    ];

    /**
     * Get the creator of this rota.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this rota.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * Get the last employee of this rota.
     */
    public function employee(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Get the last rota_templates of this rota.
     */
    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }

    /**
     * Get the last rota_templates of this rota.
     */
    public function rota_template(){
        return $this->belongsTo(Rota_template::class,'rota_templates_id');
    }
}
