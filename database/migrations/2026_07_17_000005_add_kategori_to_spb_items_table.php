<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriToSpbItemsTable extends Migration
{
    public function up()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            // Kode kategori: A=Aset, B=Consumable, C=Sparepart, D=Tools, E=Jasa, F=Maintenance, G=Stationary, H=Lain-lain
            $table->string('kategori', 5)->nullable()->after('material_code');
        });
    }

    public function down()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
}