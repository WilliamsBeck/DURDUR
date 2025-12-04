<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            // Tambahkan kolom email setelah nama kasir
            $table->string('customer_email')->nullable()->after('cashier_name');
        });
    }

    public function down()
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });
    }
};
