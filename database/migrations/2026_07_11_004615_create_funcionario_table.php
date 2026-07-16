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
        Schema::create('funcionario', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 20)->unique();
            $table->string('senha', 255);
            $table->string('cpf', 11)->unique();
            $table->string('rg', 20)->unique();
            $table->date('data_nascimento');
            $table->string('sexo', 20);
            $table->decimal('salario', 8, 2);
            $table->string('perfil', 10);
            $table->timestamps();
        });

        // Usado para criar o check em perfil e definir que salario deve ser maior que 0
        DB::statement("ALTER TABLE funcionario ADD CONSTRAINT chk_funcionario_salario CHECK (salario >= 0)");
        DB::statement("ALTER TABLE funcionario ADD CONSTRAINT chk_funcionario_perfil CHECK (perfil IN ('GERENTE', 'VENDEDOR'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionario');
    }
};
