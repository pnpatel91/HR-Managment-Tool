<?php

namespace App;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    public static function defaultPermissions()
    {
        return [
            'view role',
            'create role',
            'edit role',
            'delete role',

            'view user',
            'create user',
            'edit user',
            'delete user',

            'view company',
            'create company',
            'edit company',
            'delete company',

            'view branch',
            'create branch',
            'edit branch',
            'delete branch',

            'view attendance',
            'create attendance',
            'edit attendance',
            'delete attendance',

            'view department',
            'create department',
            'edit department',
            'delete department',

            'view holiday',
            'create holiday',
            'edit holiday',
            'delete holiday',
        ];
    }

    public function isDeleteLabel()
    {
        return Str::contains($this->name, 'delete') ? 'text-danger' : null;
    }
}
