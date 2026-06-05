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
        Schema::create('kepengurusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_userdetail')->constrained('user_details')->onDelete('cascade');
            $table->string('userName')->unique();
            $table->string('fullName');
            $table->string('address');
            $table->string('telephone_number');
            $table->foreignId('id_gender')->constrained('genders')->onDelete('cascade');
            $table->enum('divisi', ['Ketua', 'Sekretaris', 'Bendahara', 'Pemilah', 'Penimbang']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepengurusans');
    }
};
