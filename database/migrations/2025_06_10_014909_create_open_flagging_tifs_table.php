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
        Schema::create('open_flagging_tifs', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_id')->unique();
            $table->text('wilayah');
            $table->text('nama_nasabah');
            $table->string('notas')->nullable();
            $table->string('nik')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('sk_lulus')->nullable();
            $table->enum('status_permintaan', ['1', '2', '3', '4', '5', '6']);
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
        Schema::dropIfExists('open_flagging_tifs');
    }
};
