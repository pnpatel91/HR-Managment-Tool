<?php

use App\Holiday;
use App\User;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holiday = new Holiday();
        $holiday->name = "New Year's Day";
        $holiday->holiday_date = "2021-01-01";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Good Friday";
        $holiday->holiday_date = "2021-04-02";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Easter Monday";
        $holiday->holiday_date = "2021-04-05";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Early May Bank Holiday";
        $holiday->holiday_date = "2021-05-03";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Spring Bank Holiday";
        $holiday->holiday_date = "2021-05-31";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Summer Bank Holiday";
        $holiday->holiday_date = "2021-08-30";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Christmas Day";
        $holiday->holiday_date = "2021-12-27";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();

        $holiday = new Holiday();
        $holiday->name = "Boxing Day";
        $holiday->holiday_date = "2021-12-28";
        $holiday->created_by = 1;
        $holiday->updated_by = 1;
        $holiday->save();
    }
}
