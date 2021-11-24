<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(UserHasBranchesSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(UserHasDepartmentSeeder::class);
        $this->call(RotaSeeder::class);
        $this->call(HolidaySeeder::class);
        $this->call(HolidaysHasBranchesSeeder::class);
        $this->call(RotaTemplateSeeder::class);
        
    }
}
