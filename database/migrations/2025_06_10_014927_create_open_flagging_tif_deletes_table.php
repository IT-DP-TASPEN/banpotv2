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
        Schema::create('open_flagging_tif_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_id');
            $table->text('wilayah');
            $table->text('nama_nasabah');
            $table->string('notas')->nullable();
            $table->string('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('sk_lunas')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11']); //1.request, 2.checked by mitra, 3.approved by mitra, 4.rejected by mitra, 5. canceled by mitra ,6.checked by bank dp taspen, 7.approved by bank dp taspen, 8.rejected by bank dp taspen , 9.On Process, 10. Success, 11. Failed
            $table->text('keterangan')->nullable();
            $table->string('bukti_hasil')->nullable();
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
        Schema::dropIfExists('open_flagging_tif_deletes');
    }
};