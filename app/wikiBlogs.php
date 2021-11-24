<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wikiBlogs extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'category_id', 'parent_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    /**
     * Get the parent wikiBlog of this wikiBlog.
     */
    public function parent()
    {
        return $this->belongsTo(wikiBlogs::class, 'parent_id');
    }

    /**
     * Get the children wikiBlog of this wikiBlog.
    */
    public function children()
    {
        return $this->hasMany(wikiBlogs::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren')->where('status', 'Active');
    }

    public function fullChildren()
    {
        return $this->children()->with('fullChildren');
    }
    
    /**
     * Get the category of this wikiBlog.
     */
    public function category()
    {
        return $this->belongsTo(wikiCategories::class, 'category_id');
    }

    /**
     * Get the creator of this wikiCategories.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this wikiCategories.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }
}
