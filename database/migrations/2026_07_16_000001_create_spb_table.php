<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpbTable extends Migration
{
    public function up()
    {
        Schema::create('spb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_spb')->unique(); // format: SPB-YYYYMMDD-xxxx (auto generate)
            $table->string('divisi')->nullable();
            $table->text('keperluan')->nullable(); // alasan/keperluan pengajuan
            $table->date('request_date');

            // Status keseluruhan, mengikuti flow:
            // Menunggu Approval -> Ditolak (stop) / Permintaan Pengadaan -> Disposisi (bisa looping ke Permintaan Pengadaan)
            // -> PO Diterbitkan -> Resolusi -> Invoice -> Payment -> Selesai
            $table->string('status')->default('Menunggu Approval');

            // Tahap 1: Approval oleh atasan
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();

            // Tahap 2: Disposisi oleh bagian Pengadaan (approve lanjut ke PO / tidak -> balik ke Permintaan Pengadaan)
            $table->string('disposisi_by')->nullable();
            $table->timestamp('disposisi_at')->nullable();
            $table->text('disposisi_note')->nullable();

            // Tahap 3: Purchase Order
            $table->string('po_number')->nullable();
            $table->date('po_date')->nullable();
            $table->string('po_supplier')->nullable();
            $table->decimal('po_total', 15, 2)->nullable();

            // Tahap 4: Resolusi (barang diterima/dicocokkan)
            $table->text('resolusi_note')->nullable();
            $table->timestamp('resolusi_at')->nullable();

            // Tahap 5: Invoice
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();

            // Tahap 6: Payment
            $table->date('payment_date')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->string('payment_method')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spb');
    }
}
