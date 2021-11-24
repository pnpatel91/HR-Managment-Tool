<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Super Admin';
        //$user->email = 'superadmin@gmail.com';
        $user->email = 'parth.onfuro@gmail.com';
        $user->position = 'Owner';
        $user->biography = '<p>Super Admin&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('superadmin');

        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@gmail.com';
        $user->position = 'Owner';
        $user->biography = '<p>Admin&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('admin');

        $user = new User();
        $user->name = 'Management Member';
        $user->email = 'management@gmail.com';
        $user->position = 'Manager';
        $user->biography = '<p>Management&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('management');

        $user = new User();
        $user->name = 'Staff Member';
        $user->email = 'staff@gmail.com';
        $user->position = 'Staff';
        $user->biography = '<p>Staff&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('staff');

        $user = new User();
        $user->name = 'Accountant Member';
        $user->email = 'accounting@gmail.com';
        $user->position = 'Accountant';
        $user->biography = '<p>accounting&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('accounting');

        $user = new User();
        $user->name = 'Staff Member 2';
        $user->email = 'staff2@gmail.com';
        $user->position = 'Staff';
        $user->biography = '<p>Staff&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('staff');

    }
}
