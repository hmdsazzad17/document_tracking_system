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
        Schema::create('document_diff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_user_id');
            $table->string('version');
            $table->json('body_diff');
            $table->json('tags_diff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_diff');
    }
};
