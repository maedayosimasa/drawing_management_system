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
        Schema::create('meeting_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->string('meeting_log_name')->nullable();
            $table->string('meeting_log_view_path')->nullable();
            $table->string('meeting_log_pdf_path')->nullable();
            $table->string('delivery_documents_name')->nullable();
            $table->string('delivery_documents_view_path')->nullable();
            $table->string('delivery_documents_pdf_path')->nullable();
            $table->string('bidding_documents_name')->nullable();
            $table->string('bidding_documents_view_path')->nullable();
            $table->string('bidding_documents_pdf_path')->nullable();
            $table->string('archived_photo_name')->nullable();
            $table->string('archived_photo_view_path')->nullable();
            $table->string('archived_photo_pdf_path')->nullable();
            $table->string('contract_name')->nullable();
            $table->string('contract_view_path')->nullable();
            $table->string('contract_pdf_path')->nullable();
            $table->string('management_documents_name')->nullable();
            $table->string('management_documents_view_path')->nullable();
            $table->string('management_documents_pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_logs');
    }
};
