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
        Schema::table('user_transactions', function (Blueprint $table) {
            $table->dropForeign(['id_bank']);
            $table->dropColumn('id_bank');
            $table->dropColumn('nomor_rekening');
            $table->foreignId('id_userbank')->after('id_userdetail')->constrained('user_bank')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_transactions', function (Blueprint $table) {
            //
        });
    }
};
