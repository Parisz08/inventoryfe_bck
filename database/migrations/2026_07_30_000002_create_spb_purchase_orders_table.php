<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpbPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('spb_purchase_orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('spb_id');
            $table->unsignedInteger('vendor_id')->nullable();
            $table->string('supplier')->nullable();
            $table->string('po_number');
            $table->date('po_date')->nullable();
            $table->decimal('po_total', 15, 2)->default(0);
            // status: 'PO Diterbitkan' -> 'Resolusi' -> 'Invoice' -> 'Selesai'
            $table->string('status')->default('PO Diterbitkan');
            $table->text('resolusi_note')->nullable();
            $table->timestamp('resolusi_at')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('spb_id')->references('id')->on('spb')->onDelete('cascade');
            $table->index('vendor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('spb_purchase_orders');
    }
}