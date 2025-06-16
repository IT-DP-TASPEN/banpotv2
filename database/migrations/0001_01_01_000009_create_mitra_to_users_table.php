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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('roles')->constrained('mitra_masters')->cascadeOnDelete();
            $table->foreignId('mitra_cabang_id')->nullable()->after('mitra_id')->constrained('mitra_cabang_masters')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
