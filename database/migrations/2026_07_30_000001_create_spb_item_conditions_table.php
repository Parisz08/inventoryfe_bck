<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpbItemConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('spb_item_conditions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('spb_item_id');
            $table->unsignedInteger('vendor_id')->nullable();
            $table->integer('round')->default(1);
            $table->string('supplier')->nullable();
            $table->decimal('price', 15, 2);
            $table->text('condition_note')->nullable();
            $table->boolean('selected')->default(false);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('spb_item_id')->references('id')->on('spb_items')->onDelete('cascade');
            $table->index('vendor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('spb_item_conditions');
    }
}