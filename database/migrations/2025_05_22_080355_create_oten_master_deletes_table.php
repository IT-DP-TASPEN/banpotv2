<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oten_master_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('id_oten')->nullable();
            $table->string('trax_id')->nullable();
            $table->string('rek_replace')->nullable();
            $table->string('notas')->nullable();
            $table->string('periode')->nullable();
            $table->string('jenis_transaksi')->nullable();
            $table->text('nama_nasabah')->nullable();
            $table->text('mitra')->nullable();
            $table->text('juru_bayar')->nullable();
            $table->text('cabang')->nullable();
            $table->string('kode_otentifikasi')->nullable();
            $table->text('user')->nullable();
            $table->timestamp('log_date_time')->nullable();
            $table->text('status')->nullable();
            $table->text('status_bank')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oten_master_deletes');
    }
};
