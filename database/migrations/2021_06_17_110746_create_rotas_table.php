<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rotas', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->comment('shift start date')->useCurrent = true;
            $table->time('start_time')->comment('shift start time')->useCurrent = true;
            $table->date('end_date')->comment('shift end date')->useCurrent = true;
            $table->time('end_time')->comment('shift end time')->useCurrent = true;
            $table->time('max_start_time')->comment('max shift start time')->useCurrent = true;
            $table->time('break_start_time')->comment('break start time')->nullable()->useCurrent = true;
            $table->integer('break_time')->comment('Shift Break Time In Minutes')->default('0');
            $table->enum('over_time', ['Yes', 'No'])->default('No');
            $table->enum('remotely_work', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->unsignedBigInteger('rota_template_id')->index()->nullable();
            $table->text('notes')->nullable()->comment('Admin or Employer Notes');
            $table->text('employee_notes')->nullable()->comment('Employee Notes');
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('rota_template_id')->references('id')->on('rota_templates');
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
        Schema::table('rotas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['rota_template_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('rotas');
    }
}
