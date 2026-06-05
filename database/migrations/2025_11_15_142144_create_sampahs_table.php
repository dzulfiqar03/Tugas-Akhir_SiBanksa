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
        Schema::create('sampah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sampah');
            $table->bigInteger('harga');
            $table->string('satuan');
            $table->enum('kategori', ['Daur Ulang', 'Non Daur Ulang']);
            $table->foreignId('id_userdetail')->constrained('user_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampah');
    }
};
