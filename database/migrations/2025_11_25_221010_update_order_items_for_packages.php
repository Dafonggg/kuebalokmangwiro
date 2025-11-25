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
        // Drop foreign key constraint first
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Add new columns
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_type')->default('product')->after('order_id'); // 'product' or 'product_package'
            $table->unsignedBigInteger('reference_id')->nullable()->after('item_type'); // product_id or package_id
            $table->json('components')->nullable()->after('subtotal'); // For package items: store package contents
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });

        // Re-add foreign key constraint (nullable)
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Migrate existing data - set reference_id to product_id for existing items
        \DB::statement('UPDATE order_items SET item_type = "product", reference_id = product_id WHERE reference_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Remove new columns
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'reference_id', 'components']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });

        // Re-add foreign key constraint (not nullable)
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
