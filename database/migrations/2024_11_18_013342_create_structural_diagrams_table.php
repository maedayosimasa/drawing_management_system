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
        Schema::create('structural_diagrams', function (Blueprint $table) {
            $table->id();
            $table->integer('drawing_id');
            $table->string('floor_plan_name')->nullable();
            $table->string('floor_plan_view_path')->nullable();
            $table->string('floor_plan_pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structural_diagrams');
    }
};
