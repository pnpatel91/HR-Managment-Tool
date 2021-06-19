<?php

use App\Branch;
use App\Holiday;
use Illuminate\Database\Seeder;

class HolidaysHasBranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = Branch::all();
        foreach ($branches as $branch) {
            $holidays = Holiday::all();
            foreach ($holidays as $holiday) {
                $holiday->branches()->attach($branch->id);
            }
        }   
    }
}
