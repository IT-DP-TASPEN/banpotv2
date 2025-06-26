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
            $table->enum('jenis_akun', ['orang', 'badan']);
            $table->string('notas')->nullable();
            $table->string('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('dati2')->nullable(); //
            $table->text('kecamatan')->nullable(); //
            $table->text('kelurahan')->nullable(); //
            $table->text('kode_pos')->nullable(); //
            $table->text('alamat')->nullable();
            $table->enum('agama', ['1', '2', '3', '4', '5', '6', '7'])->nullable(); // 1. Islam 2. Kristen 3. Katolik 4. Hindu 5. Budha 6. Konghucu 7. Lain-lain
            $table->enum('sex', ['1', '2'])->nullable(); // 1. Laki Laki 2. Perempuan
            $table->enum('pendidikan', ['0100', '0101', '0102', '0103', '0104', '0105', '0106', '0199'])->nullable(); // 00. Tanpa Gelar , 01. Diploma I , 02. Diploma II 03. Diploma III (D3) 04. Sarjana (S1) 05. Pasca Sarjana(S2) 06. Doktoral (S3) 99. Lainnya
            $table->string('no_handphone')->nullable();
            $table->enum('status_nikah', ['1', '2', '3', '4'])->nullable(); // 1. Tidak menikah 2. Menikah 3. Cerai Hidup 4. Cerai Mati
            $table->text('nama_pasangan')->nullable();
            $table->string('nik_pasangan')->nullable();
            $table->text('nama_ibu_kandung')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->text('nama_ahli_waris')->nullable();
            $table->enum('hub_ahli_waris', ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '99'])->nullable();
            $table->string('form_buka_tab')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11']); //1.request, 2.checked by mitra, 3.approved by mitra, 4.rejected by mitra, 5. canceled by mitra ,6.checked by bank dp taspen, 7.approved by bank dp taspen, 8.rejected by bank dp taspen , 9.On Process, 10. Success, 11. Failed
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