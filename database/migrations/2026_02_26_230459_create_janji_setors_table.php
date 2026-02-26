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
        Schema::create('janji_setors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_userdetail')->constrained('user_details')->onDelete('cascade');
            $table->foreignId('id_jadwal')->constrained('jadwal_pelaksanaan')->onDelete('cascade');
            $table->time('waktu_janji')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('janji_setors');
    }
};
