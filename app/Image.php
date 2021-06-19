<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the bag record associated with the hub.
     */
    public function profileImage()
    {
        return $this->belongsTo('App\User', 'imageable_id', 'id');
    }

    public function getProfileImageLinkAttribute()
    {
        return  asset(config('const.profile.image.post') . $this->filename);
    }
}
