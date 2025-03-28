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
        Schema::create('bim_drawings', function (Blueprint $table) {
            $table->id();
            $table->integer('drawing_id');
            $table->string('bim_drawing_name')->nullable();
            $table->string('bim_drawing_view_path')->nullable();
            $table->string('bim_drawing_pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bim_drawings');
    }
};
