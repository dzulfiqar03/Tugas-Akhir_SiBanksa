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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('id_user')->constrained('users')->onDelete('cascade');
            $table->string('userName')->unique();
            $table->string('fullName');
            $table->foreignId('id_rt')->constrained('rt_perumahan')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('telephone_number');
            $table->foreignId('id_gender')->constrained('genders')->onDelete('cascade');
            $table->foreignId('id_roles')->constrained('roles')->onDelete('cascade');
            $table->enum('status', ['Pending', 'Pengajuan Verifikasi', 'Ditolak', 'Disetujui']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
