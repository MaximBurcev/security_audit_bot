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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->text('content');
            $table->unsignedBigInteger('project_id');
            $table->index('project_id', 'report_project_idx');
            $table->foreign('project_id')->on('projects')->references('id');
            $table->unsignedBigInteger('utility_id');
            $table->index('utility_id', 'report_utility_idx');
            $table->foreign('utility_id')->on('utilities')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
