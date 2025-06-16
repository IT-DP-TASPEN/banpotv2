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
        Schema::create('parameter_biaya_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('biaya_id');
            $table->foreignId('mitra_id')->constrained('mitra_masters')->cascadeOnDelete();
            $table->decimal('biaya_checking', 20, 2);
            $table->decimal('biaya_check_estimasi', 20, 2);
            $table->decimal('biaya_flagging_pensiun', 20, 2);
            $table->decimal('biaya_flagging_prapen', 20, 2);
            $table->decimal('biaya_flagging_tht', 20, 2);
            $table->decimal('biaya_flagging_prapen_tht', 20, 2);
            $table->decimal('biaya_flagging_mutasi_tif', 20, 2);
            $table->decimal('biaya_flagging_mutasi_tos', 20, 2);
            $table->decimal('ppn', 20, 2);
            $table->decimal('pph', 20, 2);
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
        Schema::dropIfExists('parameter_biaya_deletes');
    }
};
