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
        Schema::create('banpot_master_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('banpot_id');
            $table->string('rek_tabungan')->nullable();
            $table->string('nama_nasabah')->nullable();
            $table->string('notas')->nullable();
            $table->string('rek_kredit')->nullable();
            $table->string('tenor')->nullable();
            $table->string('angsuran_ke')->nullable();
            $table->date('tat_kredit')->nullable();
            $table->date('tmt_kredit')->nullable();
            $table->decimal('gaji_pensiun', 20, 2)->nullable();
            $table->decimal('nominal_potongan', 20, 2)->nullable();
            $table->decimal('saldo_mengendap', 20, 2)->nullable();
            $table->decimal('jumlah_tertagih', 20, 2)->nullable();
            $table->decimal('pinbuk_sisa_gaji', 20, 2)->nullable();
            $table->decimal('saldo_after_pinbuk', 20, 2)->default(0);
            $table->string('bank_transfer')->nullable();
            $table->string('rek_transfer')->nullable();
            $table->enum('status_banpot', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11']); //1.request, 2.checked by mitra, 3.approved by mitra, 4.rejected by mitra, 5. canceled by mitra ,6.checked by bank dp taspen, 7.approved by bank dp taspen, 8.rejected by bank dp taspen , 9.On Process, 10. Success, 11. Failed
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
        Schema::dropIfExists('banpot_master_deletes');
    }
};