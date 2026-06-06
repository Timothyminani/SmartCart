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
        Schema::create('ai_comparisons', function (Blueprint $table) {

            $table->id();

            // compared product ids
            $table->json('product_ids');

            // ai response
            $table->json('result')->nullable();

            // processing status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            // optional error message
            $table->text('error')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_comparisons');
    }
};