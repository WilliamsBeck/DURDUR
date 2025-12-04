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
        Schema::table('sales_transaction_details', function (Blueprint $table) {
            // Menambah kolom price (harga satuan) setelah quantity
            $table->bigInteger('price')->default(0)->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('sales_transaction_details', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
