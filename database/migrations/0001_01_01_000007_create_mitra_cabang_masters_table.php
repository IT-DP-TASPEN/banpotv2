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
        Schema::create('mitra_cabang_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignID('mitra_id')->constrained('mitra_masters')->cascadeOnDelete();
            $table->string('cabang_id')->unique();
            $table->text('nama_cabang');
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('mitra_cabang_masters');
    }
};
