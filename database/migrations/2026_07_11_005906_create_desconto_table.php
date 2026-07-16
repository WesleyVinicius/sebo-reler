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
        Schema::create('desconto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_funcionario')
                ->constrained('funcionario')
                ->restrictOnDelete();
            $table->decimal('porcent_desconto', 8, 2);
            $table->decimal('valor_minimo', 8, 2);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE desconto ADD CONSTRAINT chk_desconto_porcentagem CHECK (porcent_desconto BETWEEN 0 AND 100)");
        DB::statement("ALTER TABLE desconto ADD CONSTRAINT chk_desconto_valor_minimo CHECK (valor_minimo >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desconto');
    }
};
