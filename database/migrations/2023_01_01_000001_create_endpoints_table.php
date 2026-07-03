<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('method')->default('GET');
            $table->integer('expected_status_code')->default(200);
            $table->integer('timeout')->default(30);
            $table->integer('interval_minutes')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_pinged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('endpoints');
    }
};
