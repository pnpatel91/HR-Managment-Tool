<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotaTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rota_templates', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_at')->comment('shift start time & date')->useCurrent = true;
            $table->timestamp('end_at')->comment('shift end time & date')->useCurrent = true;
            $table->time('break_time')->comment('Shift Break Time In Minutes')->default('0');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rota_templates');
    }
}
