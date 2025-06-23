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
        Schema::create('query_logs', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type')->index();
            $table->string('query_params')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->float('response_time_ms');
            $table->integer('response_status_code');
            $table->string('endpoint');
            $table->json('query_data')->nullable();
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at');

            // Indexes for efficient querying
            $table->index(['created_at', 'resource_type']);
            $table->index(['created_at', 'endpoint']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_logs');
    }
};
