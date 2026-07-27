<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorSelectedToSpbConditionsTable extends Migration{
    public function up()
    {
        Schema::table('spb_conditions', function (Blueprint $table) {
            $table->integer('vendor_id')->unsigned()->nullable()->after('spb_id');
            $table->boolean('selected')->default(false)->after('condition_note');
        });
    }

    public function down()
    {
        Schema::table('spb_conditions', function (Blueprint $table) {
            $table->dropColumn(['vendor_id', 'selected']);
        });
    }
}
