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
            // Menambahkan kolom email (nullable agar aman untuk data lama)
            // 'after' berfungsi meletakkan kolom ini setelah kolom supplier_name
            $table->string('supplier_email')->nullable()->after('supplier_name');
        });
    }

    public function down()
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn('supplier_email');
        });
    }
};
