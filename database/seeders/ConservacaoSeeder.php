<?php

namespace Database\Seeders;

use App\Http\Controllers\ConservacaoController;
use App\Models\Conservacao;
use Illuminate\Database\Seeder;

class ConservacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ConservacaoController::OPCOES_ESTADO as $estado){
            Conservacao::firstOrCreate(['estado' => $estado]);
        }
    }
}
