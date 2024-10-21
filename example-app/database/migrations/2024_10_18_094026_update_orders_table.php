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
            $table->string('firstname', 32)->after('id')->index();
            $table->string('lastname', 32)->after('firstname')->index(); 

            $table->dropIndex(['customer_name']);
            $table->dropColumn('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name', 32)->after('id')->index();
            $table->dropColumn(['firstname', 'lastname']);
        });
    }
};
