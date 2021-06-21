<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('approved_by')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->date('start_at')->comment('Leave Start Date')->useCurrent = true;
            $table->date('end_at')->comment('Leave End Date')->useCurrent = true;
            $table->integer('leave_days');
            $table->enum('leave_type', ['Annual', 'Sick', 'Hospitalisation', 'Maternity', 'Paternity', 'LOP'])->comment('Maternity Assigned to female only, Paternity Assigned to male only, LOP - Loss of Pay Leave (Unpaid)');
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->enum('half_day', ['Full Day', 'Half Day']);
            $table->enum('status', ['New', 'Approved', 'Declined']);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['branch_id']);
        });
        Schema::dropIfExists('leaves');
    }
}
