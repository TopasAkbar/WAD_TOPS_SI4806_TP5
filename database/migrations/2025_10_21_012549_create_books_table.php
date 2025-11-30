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
        // ========== 1 =========
        // Create books table with necessary fields
        // Fields: id, title, author, published_year, is_available, created_at, updated_at
        Schema::create('books', function (Blueprint $table) {
            $table->id(); 
            $table->string('title'); //judul buku
            $table->string('author'); //siapa penulisnya
            $table->year('published_year'); //Tahun buku itu diterbit
            $table->boolean('is_available')->default(true); //Status ketersediaan buku
            $table->timestamps(); //menampilkan informasi tentang created_at dan updated_at (status)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
