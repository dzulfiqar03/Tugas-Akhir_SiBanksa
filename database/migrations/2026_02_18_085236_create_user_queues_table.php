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
        Schema::create('user_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_userdetail')->constrained('user_details')->onDelete('cascade');
            $table->foreignId('id_jadwal')->constrained('jadwal_pelaksanaan')->onDelete('cascade');
            $table->string('queue_number');
            $table->enum('status', ['waiting', 'processing', 'finished', 'skipped']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_queues');
    }
};
