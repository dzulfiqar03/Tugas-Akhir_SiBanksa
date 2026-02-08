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
        Schema::create('open_street_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_geoLoc')->constrained('user_geolocations')->onDelete('cascade');
            $table->string('display_name');
            $table->decimal('latitude')->nullable();
            $table->decimal('logitude')->nullable();
            $table->string(column: 'type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_street_views');
    }
};
