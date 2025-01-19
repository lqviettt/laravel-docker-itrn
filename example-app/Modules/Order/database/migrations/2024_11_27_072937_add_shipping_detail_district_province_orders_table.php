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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
            $table->string('shipping_province', 70)->after('status');
            $table->string('shipping_district', 70)->after('shipping_province');
            $table->string('shipping_address_detail', 70)->after('shipping_district');
            $table->string('shipping_ward')->nullable()->after('shipping_address_detail');
            $table->decimal('shipping_fee', 8, 2)->nullable()->after('shipping_ward');
            $table->decimal('total_price', 10, 2)->nullable()->after('shipping_fee');
            $table->string('payment_method')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address');
            $table->dropColumn('shipping_province', 70);
            $table->dropColumn('shipping_district', 70);
            $table->dropColumn('shipping_address_detail', 70);
            $table->dropColumn('shipping_ward');
            $table->dropColumn('shipping_fee');
            $table->dropColumn('total_price');
            $table->dropColumn('payment_method');
        });
    }
};
