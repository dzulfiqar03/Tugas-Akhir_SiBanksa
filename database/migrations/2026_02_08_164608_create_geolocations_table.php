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
        Schema::create('user_geolocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_userdetail')->constrained('user_details')->onDelete('cascade');
            $table->string('amenity');
            $table->string('house_number')->nullable();
            $table->string('city')->nullable();
            $table->string(column: 'county')->nullable();
            $table->string('state')->nullable();
            $table->string(column: 'country')->default('Indonesia');
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_geolocations');
    }
};
