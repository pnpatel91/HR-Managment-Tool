<?php

use App\User_has_branches;
use Illuminate\Database\Seeder;

class UserHasBranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '1';
        $user_has_branches->user_id = '1';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '2';
        $user_has_branches->user_id = '1';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '1';
        $user_has_branches->user_id = '2';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '1';
        $user_has_branches->user_id = '3';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '1';
        $user_has_branches->user_id = '4';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '2';
        $user_has_branches->user_id = '5';
        $user_has_branches->save();

        $user_has_branches = new User_has_branches();
        $user_has_branches->branch_id = '2';
        $user_has_branches->user_id = '6';
        $user_has_branches->save();
    }
}
