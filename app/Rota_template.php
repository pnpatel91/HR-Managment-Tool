<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rota_template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'start_at', 'end_at', 'max_start_at', 'break_time', 'day_list', 'types', 'break_start_at', 'over_time', 'remotely_work', 'created_by', 'updated_by'
    ];

    /**
     * Get the creator of this rota_template.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this rota_template.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * Get all of the leaves for the branch.
     */
    public function rotas()
    {
        return $this->hasMany(Rota::class);
    }
}
