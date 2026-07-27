<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerekSpecificationToSpbItemsTable extends Migration
{
    public function up()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            $table->string('merek')->nullable()->after('material_name');
            $table->string('specification')->nullable()->after('merek');
        });
    }

    public function down()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            $table->dropColumn(['merek', 'specification']);
        });
    }
}