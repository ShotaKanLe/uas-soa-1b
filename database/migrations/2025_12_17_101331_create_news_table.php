<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Blok ini hanya untuk MEMBUAT skema tabel
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('publisher'); 
            $table->string('image')->nullable(); 
            $table->timestamps();
            
            // Kolom untuk Soft Deletes
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Blok ini hanya untuk MENGHAPUS tabel
        Schema::dropIfExists('news');
    }
};