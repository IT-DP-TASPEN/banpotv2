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
        Schema::create('permintaan_mutasi_tos', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_id')->unique();
            $table->text('wilayah');
            $table->text('nama_nasabah');
            $table->string('notas')->nullable();
            $table->string('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_handphone')->nullable();
            $table->string('ktp')->nullable();
            $table->string('form_sp3r')->nullable();
            $table->string('sk_pensiun')->nullable();
            $table->string('foto_tab')->nullable();
            $table->string('lampiran_persyaratan')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6']);
            $table->text('keterangan')->nullable();
            $table->string('bukti_hasil')->nullable();
            $table->foreignId('biaya_id')->constrained('parameter_biayas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_mutasi_tos');
    }
};
