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
        Schema::create('identitas_mitra_masters', function (Blueprint $table) {
            $table->id();
            $table->string('identity_id')->unique();
            $table->foreignId('mitra_id')->constrained('mitra_masters');
            $table->string('notas');
            $table->text('nama_nasabah');
            $table->string('rek_tabungan');
            $table->string('rek_replace');
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
        Schema::dropIfExists('identitas_mitra_masters');
    }
};
