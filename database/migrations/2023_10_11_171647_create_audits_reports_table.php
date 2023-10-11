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
        Schema::create('audits_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('audit_id');
            $table->unsignedBigInteger('report_id');

            // IDX
            $table->index('audit_id', 'audit_report_audit_idx');
            $table->index('report_id', 'audit_report_report_idx');

            // FK
            $table->foreign('audit_id', 'audit_report_audit_fk')->on('audits')->references('id');
            $table->foreign('report_id', 'audit_report_report_fk')->on('reports')->references('id');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits_reports');
    }
};
