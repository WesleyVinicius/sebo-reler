<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exemplar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_livro')
                ->constrained('livro')
                ->restrictOnDelete();
            $table->foreignId('id_conservacao')
                ->constrained('conservacao')
                ->restrictOnDelete();
            $table->foreignId('id_venda')
                ->nullable()
                ->constrained('venda')
                ->restrictOnDelete();
            $table->foreignId('id_funcionario')
                ->constrained('funcionario')
                ->restrictOnDelete();
            $table->decimal('preco_compra', 10, 2);
            $table->decimal('preco_venda', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE exemplar ADD CONSTRAINT chk_exemplar_preco_compra CHECK (preco_compra >= 0)");
        DB::statement("ALTER TABLE exemplar ADD CONSTRAINT chk_exemplar_preco_venda CHECK (preco_venda >= 0)");
        DB::statement("
            ALTER TABLE exemplar ADD CONSTRAINT chk_exemplar_status_venda
            CHECK ( (status = TRUE AND id_venda IS NULL) OR (status = FALSE AND id_venda IS NOT NULL) )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exemplar');
    }
};
