<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->cascadeOnDelete();
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('is_success');
            $table->text('error_message')->nullable();
            $table->timestamp('pinged_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ping_logs');
    }
};
