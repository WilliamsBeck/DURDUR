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
        Schema::table('supplier', function (Blueprint $table) {
            // Tipe 'text' cocok untuk alamat panjang
            // Ditaruh setelah supplier_phone
            $table->text('supplier_address')->nullable()->after('supplier_phone');
        });
    }

    public function down()
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn('supplier_address');
        });
    }
};
