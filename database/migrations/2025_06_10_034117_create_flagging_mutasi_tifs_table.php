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
        Schema::create('flagging_mutasi_tifs', function (Blueprint $table) {
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
            $table->string('rek_tabungan')->nullable();
            $table->string('rek_kredit')->nullable();
            $table->date('tat_kredit')->nullable();
            $table->string('ktp')->nullable();
            $table->string('sp_deb_flagging')->nullable();
            $table->string('foto_tab')->nullable();
            $table->string('form_pindah_kantor')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11']); //1.request, 2.checked by mitra, 3.approved by mitra, 4.rejected by mitra, 5. canceled by mitra ,6.checked by bank dp taspen, 7.approved by bank dp taspen, 8.rejected by bank dp taspen , 9.On Process, 10. Success, 11. Failed
            $table->text('keterangan')->nullable();
            $table->string('bukti_hasil')->nullable();
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
        Schema::dropIfExists('flagging_mutasi_tifs');
    }
};