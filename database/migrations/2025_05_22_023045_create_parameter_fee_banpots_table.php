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
        Schema::create('parameter_fee_banpots', function (Blueprint $table) {
            $table->id();
            $table->string('fee_id')->unique();
            $table->foreignId('mitra_id')->constrained('mitra_masters')->cascadeOnDelete();
            $table->enum('jenis_fee', ['1', '2']); //1.Dapem 2.Tagihan
            $table->decimal('fee_banpot', 20, 2);
            $table->decimal('saldo_mengendap', 20, 2);
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
        Schema::dropIfExists('parameter_fee_banpots');
    }
};