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
        Schema::create('query_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('statistic_type')->unique();
            $table->json('data');
            $table->float('average_response_time')->nullable();
            $table->integer('total_queries')->nullable();
            $table->timestamp('computed_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_statistics');
    }
};
