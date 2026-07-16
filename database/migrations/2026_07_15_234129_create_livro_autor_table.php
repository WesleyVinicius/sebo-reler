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
        Schema::create('livro_autor', function (Blueprint $table) {
            $table->foreignId('id_livro')
                ->constrained('livro')
                ->cascadeOnDelete();
            $table->foreignId('id_autor')
                ->constrained('autor')
                ->restrictOnDelete();

            $table->primary(['id_livro', 'id_autor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_autor');
    }
};
