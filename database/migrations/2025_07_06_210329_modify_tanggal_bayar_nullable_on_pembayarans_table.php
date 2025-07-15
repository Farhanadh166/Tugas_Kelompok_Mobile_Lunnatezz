<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->datetime('tanggal_bayar')->nullable()->change();
        });
    }
 
    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->datetime('tanggal_bayar')->nullable(false)->change();
        });
    }
};
