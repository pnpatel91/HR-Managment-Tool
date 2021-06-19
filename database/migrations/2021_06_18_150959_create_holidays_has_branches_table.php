<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHolidaysHasBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holidays_has_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->timestamps();
            
            $table->foreign('holiday_id')->references('id')->on('holidays')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holidays_has_branches', function (Blueprint $table) {
            $table->dropForeign(['holiday_id']);
            $table->dropForeign(['branch_id']);
        });
        Schema::dropIfExists('holidays_has_branches');
    }
}
