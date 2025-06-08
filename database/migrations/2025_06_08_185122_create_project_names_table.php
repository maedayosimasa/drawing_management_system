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
        Schema::create('project_names', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('project_name');
            $table->string('address')->nullable();
            $table->string('client')->nullable();
            $table->date('construction_period_start')->nullable();
            $table->date('construction_period_end')->nullable();
            $table->date('completion_date')->nullable();
            $table->bigInteger('constract_amount')->nullable();
            $table->string('use')->nullable();
            $table->integer('site_area')->nullable();
            $table->integer('building_area')->nullable();
            $table->integer('total_floor_area')->nullable();
            $table->string('strural')->nullable();
            $table->integer('floor_number_underground')->nullable();
            $table->integer('floor_number_ground')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_names');
    }
};
