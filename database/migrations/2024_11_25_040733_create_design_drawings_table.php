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
        Schema::create('design_drawings', function (Blueprint $table) {
            $table->id();
            $table->integer('drawing_id');
            $table->string('finishing_table_name')->nullable();
            $table->string('finishing_table_view_path')->nullable();
            $table->string('finishing_table_pdf_path')->nullable();
            $table->string('layout_diagram_name')->nullable();
            $table->string('layout_diagram_view_path')->nullable();
            $table->string('layout_diagram_pdf_path')->nullable();
            $table->string('floor_plan_name')->nullable();
            $table->string('floor_plan_view_path')->nullable();
            $table->string('floor_plan_pdf_path')->nullable();
            $table->string('elevation_name')->nullable();
            $table->string('elevation_view_path')->nullable();
            $table->string('elevation_pdf_path')->nullable();
            $table->string('sectional_name')->nullable();
            $table->string('sectional_view_path')->nullable();
            $table->string('sectional_pdf_path')->nullable();
            $table->string('design_drawing_all_name')->nullable();
            $table->string('design_drawing_all_view_path')->nullable();
            $table->string('design_drawing_all_pdf_path')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_drawings');
    }
};
