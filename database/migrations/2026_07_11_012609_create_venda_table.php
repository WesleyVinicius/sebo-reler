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
        Schema::create('venda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_funcionario')
                ->constrained('funcionario')
                ->restrictOnDelete();
            $table->foreignId('id_cliente')
                ->constrained('cliente')
                ->restrictOnDelete();
            $table->timestamp('data')->useCurrent();
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_desconto', 10, 2)->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE venda ADD CONSTRAINT chk_venda_valor_total CHECK (valor_total >= 0)");
        DB::statement("ALTER TABLE venda ADD CONSTRAINT chk_venda_valor_desconto CHECK (valor_desconto >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda');
    }
};
