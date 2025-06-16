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
        Schema::create('dapem_ned_master_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('notas')->nullable();
            $table->text('nama_nasabah')->nullable();
            $table->string('kantor_bayar')->nullable();
            $table->string('jiwa')->nullable();
            $table->string('jenis')->nullable();
            $table->decimal('nominal_dapem', 20, 2)->nullable();
            $table->string('rek_replace')->nullable();
            $table->string('bulan_dapem')->nullable();
            $table->string('code1')->nullable();
            $table->string('code2')->nullable();
            $table->string('code3')->nullable();
            $table->string('code4')->nullable();
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
        Schema::dropIfExists('dapem_ned_master_deletes');
    }
};
