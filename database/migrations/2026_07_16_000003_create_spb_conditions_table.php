<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpbConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('spb_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('spb_id')->unsigned();
            $table->integer('round')->default(1); // ke berapa kali revisi kondisi (bertambah tiap kali disposisi = Tidak)
            $table->string('supplier')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('condition_note')->nullable(); // syarat & ketentuan, hasil nego, dsb
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index('spb_id');
            $table->foreign('spb_id')->references('id')->on('spb')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('spb_conditions');
    }
}
