<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update empty tanggal_bayar values to NULL first
        DB::table('pembayarans')->where('tanggal_bayar', '')->update(['tanggal_bayar' => null]);
        
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->datetime('tanggal_bayar')->nullable()->change();
        });
    }
 
    public function down()
    {
        // Set default value for NULL tanggal_bayar before making it NOT NULL
        DB::table('pembayarans')->whereNull('tanggal_bayar')->update(['tanggal_bayar' => now()]);
        
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->datetime('tanggal_bayar')->nullable(false)->change();
        });
    }
};
