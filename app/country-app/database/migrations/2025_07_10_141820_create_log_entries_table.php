<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_use_app', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->timestamp('request_timestamp');
            $table->integer('num_countries_returned');
            $table->text('countries_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_use_app');
    }
};
