<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderIdToSpbItemsTable extends Migration
{
    public function up()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            $table->unsignedInteger('spb_purchase_order_id')->nullable()->after('spb_id');
            $table->foreign('spb_purchase_order_id')->references('id')->on('spb_purchase_orders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('spb_items', function (Blueprint $table) {
            $table->dropForeign(['spb_purchase_order_id']);
            $table->dropColumn('spb_purchase_order_id');
        });
    }
}