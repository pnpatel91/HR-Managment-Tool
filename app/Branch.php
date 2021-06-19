<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'city', 'state', 'postcode', 'country', 'latitude', 'longitude', 'radius', 'company_id', 'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    /**
     * Get the creator that owns the branch.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor that owns the branch.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * Get the company that owns the branch.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * The users that belong to the branch.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_branches');
    }

    /**
     * The holidays that belong to the branch.
     */
    public function branches()
    {
        return $this->belongsToMany(Holiday::class, 'holidays_has_branches');
    }
}
