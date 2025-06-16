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
        Schema::create('pembukaan_rekening_barus', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_id')->unique();
            $table->text('wilayah');
            $table->text('nama_nasabah');
            $table->enum('jenis_akun', ['1', '2']);
            $table->string('notas')->nullable();
            $table->string('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('no_handphone')->nullable();
            $table->enum('status_nikah', ['1', '2', '3', '4', '5', '6'])->nullable();
            $table->text('nama_pasangan')->nullable();
            $table->string('nik_pasangan')->nullable();
            $table->text('nama_ibu_kandung')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->string('form_buka_tab')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6']);
            $table->text('keterangan')->nullable();
            $table->string('rek_tabungan')->nullable();
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
        Schema::dropIfExists('pembukaan_rekening_barus');
    }
};