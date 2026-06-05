<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::create('push_subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignUuid('user_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->text('endpoint'); // URL unik dari browser
        $table->text('public_key');
        $table->text('auth_token');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
