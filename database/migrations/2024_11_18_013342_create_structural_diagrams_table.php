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
            $table->string('structural_floor_plan_name')->nullable();
            $table->string('structural_floor_plan_view_path')->nullable();
            $table->string('structural_floor_plan_pdf_path')->nullable();
            $table->string('structural_elevation_name')->nullable();
            $table->string('structural_elevation_view_path')->nullable();
            $table->string('structural_elevation_pdf_path')->nullable();
            $table->string('structural_sectional_name')->nullable();
            $table->string('structural_sectional_view_path')->nullable();
            $table->string('structural_sectional_pdf_path')->nullable();
            $table->string('structural_frame_diagram_name')->nullable();
            $table->string('structural_frame_diagram_view_path')->nullable();
            $table->string('structural_frame_diagram_pdf_path')->nullable();
            $table->string('structural_diagram_all_name')->nullable();
            $table->string('structural_diagram_all_view_path')->nullable();
            $table->string('structural_diagram_all_pdf_path')->nullable();
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
