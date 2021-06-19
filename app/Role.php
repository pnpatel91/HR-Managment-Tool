<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    public function isDisabled()
    {
        return $this->name === 'superadmin' ? 'disabled' : null;
    }

    public function isSuperAdmin()
    {
        return $this->name === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    public function isChecked($permission)
    {
        return $this->hasPermissionTo($permission) ? 'checked' : null;
    }

    public function getDateAttribute()
    {
        return $this->created_at->toFormattedDateString();
    }
}
