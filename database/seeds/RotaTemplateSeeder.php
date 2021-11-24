<?php

use App\Rota_template;

use Illuminate\Database\Seeder;

class RotaTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Rota_template = new Rota_template();
        $Rota_template->name = "Weekly Rota";
        $Rota_template->start_at = "09:00:00";
        $Rota_template->end_at = "18:00:00";
        $Rota_template->max_start_at = "09:10:00";
        $Rota_template->break_start_at = "02:00:00";
        $Rota_template->break_time = "30";
        $Rota_template->day_list = json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday"]);
        $Rota_template->types = "Week";
        $Rota_template->over_time = "No";
        $Rota_template->remotely_work = "No";
        $Rota_template->created_by = 1;
        $Rota_template->updated_by = 1;
        $Rota_template->save();
    }
}
