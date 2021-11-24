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
            $table->string('name', 60);
            $table->time('start_at')->comment('shift start time')->useCurrent = true;
            $table->time('end_at')->comment('shift end time')->useCurrent = true;
            $table->time('max_start_at')->comment('max shift start time')->useCurrent = true;
            $table->time('break_start_at')->comment('break start time')->nullable()->useCurrent = true;
            $table->integer('break_time')->comment('Shift Break Time In Minutes')->default('0');
            $table->json('day_list')->comment('Day list')->nullable();
            $table->enum('types', ['Day', 'Week', 'Month'])->comment('Day, Week, Month')->default('Day');
            $table->enum('over_time', ['Yes', 'No'])->comment('Yes, No')->default('No');
            $table->enum('remotely_work', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rota_templates', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        
        Schema::dropIfExists('rota_templates');
    }
}
