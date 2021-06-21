<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::defaultPermissions();

        // create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $roleSuperAdmin = Role::create(['name' => 'superadmin']);
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleManagement = Role::create(['name' => 'management']);
        $roleStaff = Role::create(['name' => 'staff']);
        $roleAccounting = Role::create(['name' => 'accounting']);

        $roleSuperAdmin->syncPermissions(Permission::all());
        $roleAdmin->syncPermissions(Permission::all());
        $roleManagement->syncPermissions(Permission::where('name', 'create attendance')
                                                ->orWhere('name', 'view holiday')
                                                ->orWhere('name', 'like', '%leave - employee')
                                                ->get());


        $roleStaff->syncPermissions(Permission::where('name', 'create attendance')
                                            ->orWhere('name', 'view holiday')
                                            ->orWhere('name', 'like', '%leave - employee')
                                            ->get());


        $roleAccounting->syncPermissions(Permission::where('name', 'create attendance')
                                                 ->orWhere('name', 'view holiday')
                                                 ->orWhere('name', 'like', '%leave - employee')
                                                 ->get());

    }
}
