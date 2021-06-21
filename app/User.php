<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Image;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'biography', 'dateOfBirth', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function images()
    {
        return $this->morphMany('App\Image', 'profileImage');
    }

    public function image()
    {
        $id = auth()->user()->id;
        $image = Image::where('imageable_id', $id)->first();
        if(isset($image->filename)) {
            return $image;
        }else{
            $image = ['filename' => 'default_profile.jpg',
                      'imageable_id' => 1,
                      'imageable_type' => 'App\Profile'];
            return (object)$image;
        }
    }

    public function getImageUrlAttribute($id = null)
    {
        if(empty($id)){
            $id = auth()->user()->id;
        }

        $image = Image::where('imageable_id', $id)->first();
        if(isset($image->filename)) {
            return asset('/storage/app/public/image/profile/'. $image->filename);
        }else{
            return asset('/public/image/default_profile.jpg');
        }
    }

    public function getDateAttribute()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The branches that belong to the user.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_has_branches')->with('company');
    }

    public function getBranchIdsAttribute()
    {
        return $this->branches->pluck('id');
    }

    /**
     * Get the creator of this attendance with branch.
     */
    public function attendance_creator(){
        return $this->belongsTo(Attendance::class,'created_by')->with('branch');
    }

    /**
     * Get the last editor of this attendance with branch.
     */
    public function attendance_editor(){
        return $this->belongsTo(Attendance::class,'updated_by')->with('branch');
    }

    /**
     * The departments that belong to the user.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_has_departments');
    }

    public function getDepartmentIdsAttribute()
    {
        return $this->departments->pluck('id');
    }

    /**
     * Get the employee leave.
     */
    public function leave_employee(){
        return $this->belongsTo(Leave::class,'employee_id')->with('branch');
    }

    /**
     * Get the approver leave.
     */
    public function leave_approved_by(){
        return $this->belongsTo(Leave::class,'approved_by')->with('branch');
    }
}
