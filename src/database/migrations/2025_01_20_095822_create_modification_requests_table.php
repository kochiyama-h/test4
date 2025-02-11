<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_record_id')->constrained()->cascadeOnDelete();
            $table->date('before_date')->nullable();
            $table->date('after_date')->nullable();
            $table->datetime('before_time')->nullable();
            $table->datetime('after_time')->nullable();
            $table->foreignId('target_break_id')->nullable()->constrained('breaks')->onDelete('cascade');
            $table->enum('type', ['clock_in', 'clock_out', 'break_start', 'break_end']);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
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
        Schema::dropIfExists('modification_requests');
    }
}
