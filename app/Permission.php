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

            'view leave - admin',
            'create leave - admin',
            'edit leave - admin',
            'delete leave - admin',

            'view leave - employee',
            'create leave - employee',
            'edit leave - employee',
            'delete leave - employee',

            'view rota',
            'create rota',
            'edit rota',
            'delete rota',

            'view rota_template',
            'create rota_template',
            'edit rota_template',
            'delete rota_template',

            'view Wiki Category',
            'create Wiki Category',
            'edit Wiki Category',
            'delete Wiki Category',

            'view Wiki Blog',
            'create Wiki Blog',
            'edit Wiki Blog',
            'delete Wiki Blog',

            'view Project Category',
            'create Project Category',
            'edit Project Category',
            'delete Project Category',
        ];
    }

    public function isDeleteLabel()
    {
        return Str::contains($this->name, 'delete') ? 'text-danger' : null;
    }
}
