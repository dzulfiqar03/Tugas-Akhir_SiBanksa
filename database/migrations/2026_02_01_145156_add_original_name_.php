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
        Schema::table('document_archivers', function (Blueprint $table) {
            $table->dropColumn('src_document');
            $table->string('original_filesname')->after('id_userdetail')->nullable();
            $table->string('encrypted_filesname')->after('original_filesname')->nullable();
        });
        Schema::table('evidence_archivers', function (Blueprint $table) {
            $table->dropColumn('src_image');
            $table->string('original_photoname')->after('id_userdetail')->nullable();
            $table->string('encrypted_photoname')->after('original_photoname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_archivers');
        Schema::dropIfExists('evidence_archivers');
    }
};
