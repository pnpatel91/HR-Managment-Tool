<?php

use App\User_has_department;
use Illuminate\Database\Seeder;

class UserHasDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '1';
        $user_has_department->save();

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '2';
        $user_has_department->save();

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '3';
        $user_has_department->save();

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '4';
        $user_has_department->save();

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '5';
        $user_has_department->save();

        $user_has_department = new User_has_department();
        $user_has_department->department_id = '1';
        $user_has_department->user_id = '6';
        $user_has_department->save();
    }
}
