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
        Schema::table('products', function (Blueprint $table) {

            // Add new columns
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');

            $table->integer('stock_quantity')->default(0)->after('sale_price');

            $table->boolean('is_active')->default(true)->after('stock_quantity');
            $table->boolean('is_featured')->default(false)->after('is_active');

            // Optional: drop old stock column if exists
            $table->dropColumn('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'sale_price',
                'stock_quantity',
                'is_active',
                'is_featured'
            ]);

            // restore stock if rollback
            $table->integer('stock')->default(0);
        });
    }
};
