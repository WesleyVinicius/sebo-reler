<?php

namespace Database\Seeders;

use App\Http\Controllers\ConservacaoController;
use App\Models\Autor;
use App\Models\Cliente;
use App\Models\Conservacao;
use App\Models\Desconto;
use App\Models\Editora;
use App\Models\Exemplar;
use App\Models\Funcionario;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\Venda;
use App\Services\CalculadoraDesconto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Dados de demonstração. Rode com:
 *   php artisan db:seed --class=DemoSeeder
 * Pode ser executado mais de uma vez sem duplicar o que já existe.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('O DemoSeeder não deve rodar em produção.');
            return;
        }

        $gerente = $this->funcionarios();
        $this->descontos($gerente);
        $conservacoes = $this->conservacoes();
        $this->livros($gerente, $conservacoes);
        $this->clientes();
        $this->vendas();

        $this->command->info('Demonstração pronta. Login: gerente.demo / demo1234');
    }

    private function funcionarios(): Funcionario
    {
        $gerente = Funcionario::firstOrCreate(
            ['usuario' => 'gerente.demo'],
            [
                'senha' => Hash::make('demo1234'),
                'cpf' => '11111111111',
                'rg' => '111111111',
                'sexo' => 'Feminino',
                'data_nascimento' => '1985-05-10',
                'salario' => 4500,
                'perfil' => 'GERENTE',
            ]
        );

        Funcionario::firstOrCreate(
            ['usuario' => 'vendedor.demo'],
            [
                'senha' => Hash::make('demo1234'),
                'cpf' => '22222222222',
                'rg' => '222222222',
                'sexo' => 'Masculino',
                'data_nascimento' => '1992-09-21',
                'salario' => 2800,
                'perfil' => 'VENDEDOR',
            ]
        );

        return $gerente;
    }

    private function descontos(Funcionario $gerente): void
    {
        if (Desconto::count() > 0) {
            return;
        }

        foreach ([[100, 5], [250, 10], [500, 15]] as [$minimo, $percentual]) {
            Desconto::create([
                'id_funcionario' => $gerente->id,
                'valor_minimo' => $minimo,
                'porcent_desconto' => $percentual,
            ]);
        }
    }

    private function conservacoes()
    {
        // Mesma lista usada pelo CRUD: uma única fonte de verdade
        foreach (ConservacaoController::OPCOES_ESTADO as $estado) {
            Conservacao::firstOrCreate(['estado' => $estado]);
        }

        return Conservacao::orderBy('id')->get()->values();
    }

    private function livros(Funcionario $gerente, $conservacoes): void
    {
        // lotes: [posição do estado de conservação, quantidade, preço de compra, preço de venda]
        $catalogo = [
            ['titulo' => 'Dom Casmurro', 'autor' => 'Machado de Assis', 'genero' => 'Romance', 'editora' => 'Editora Aurora', 'lotes' => [[0, 2, 14, 32], [2, 2, 8, 18]]],
            ['titulo' => 'Memórias Póstumas de Brás Cubas', 'autor' => 'Machado de Assis', 'genero' => 'Romance', 'editora' => 'Editora Horizonte', 'lotes' => [[1, 3, 12, 28]]],
            ['titulo' => 'Capitães da Areia', 'autor' => 'Jorge Amado', 'genero' => 'Romance', 'editora' => 'Editora Aurora', 'lotes' => [[0, 2, 18, 40], [1, 2, 12, 26]]],
            ['titulo' => 'Vidas Secas', 'autor' => 'Graciliano Ramos', 'genero' => 'Romance', 'editora' => 'Livraria Central', 'lotes' => [[1, 3, 10, 24]]],
            ['titulo' => 'A Hora da Estrela', 'autor' => 'Clarice Lispector', 'genero' => 'Romance', 'editora' => 'Editora Pau-Brasil', 'lotes' => [[0, 1, 20, 45], [2, 2, 9, 20]]],
            ['titulo' => 'Laços de Família', 'autor' => 'Clarice Lispector', 'genero' => 'Contos', 'editora' => 'Editora Pau-Brasil', 'lotes' => [[1, 2, 15, 34]]],
            ['titulo' => 'O Hobbit', 'autor' => 'J. R. R. Tolkien', 'genero' => 'Fantasia', 'editora' => 'Editora Horizonte', 'lotes' => [[0, 2, 28, 62], [1, 2, 20, 44]]],
            ['titulo' => 'Duna', 'autor' => 'Frank Herbert', 'genero' => 'Ficção científica', 'editora' => 'Livraria Central', 'lotes' => [[1, 3, 25, 55]]],
            ['titulo' => 'Fundação', 'autor' => 'Isaac Asimov', 'genero' => 'Ficção científica', 'editora' => 'Editora Aurora', 'lotes' => [[1, 2, 22, 48], [2, 2, 12, 28]]],
            ['titulo' => 'O Caso dos Dez Negrinhos', 'autor' => 'Agatha Christie', 'genero' => 'Policial', 'editora' => 'Editora Horizonte', 'lotes' => [[2, 3, 8, 19]]],
            ['titulo' => 'Quarto de Despejo', 'autor' => 'Carolina Maria de Jesus', 'genero' => 'Memórias', 'editora' => 'Livraria Central', 'lotes' => [[1, 3, 16, 36]]],
            ['titulo' => 'Sagarana', 'autor' => 'João Guimarães Rosa', 'genero' => 'Contos', 'editora' => 'Editora Pau-Brasil', 'lotes' => [[0, 1, 24, 52], [2, 2, 10, 22]]],
        ];

        foreach ($catalogo as $item) {
            $livro = Livro::firstOrCreate(
                ['titulo' => $item['titulo']],
                [
                    'id_editora' => Editora::firstOrCreate(['nome' => $item['editora']])->id,
                    'id_genero' => Genero::firstOrCreate(['nome' => $item['genero']])->id,
                ]
            );

            // Já existia (outra execução): não cria autores e exemplares de novo
            if (!$livro->wasRecentlyCreated) {
                continue;
            }

            $livro->autores()->attach(Autor::firstOrCreate(['nome' => $item['autor']])->id);

            foreach ($item['lotes'] as [$posicao, $quantidade, $compra, $venda]) {
                $conservacao = $conservacoes[$posicao % $conservacoes->count()];

                for ($i = 0; $i < $quantidade; $i++) {
                    Exemplar::create([
                        'id_livro' => $livro->id,
                        'id_conservacao' => $conservacao->id,
                        'id_funcionario' => $gerente->id,
                        'preco_compra' => $compra,
                        'preco_venda' => $venda,
                        'status' => true,
                    ]);
                }
            }
        }
    }

    private function clientes(): void
    {
        if (Cliente::count() === 0) {
            Cliente::factory()->count(10)->create();
        }
    }

    private function vendas(): void
    {
        // Se já existem vendas (reais ou de outra execução), não mexe
        if (Venda::exists()) {
            return;
        }

        $faixas = Desconto::all();
        $funcionarios = Funcionario::whereIn('usuario', ['gerente.demo', 'vendedor.demo'])->get();

        for ($i = 0; $i < 10; $i++) {
            $exemplares = Exemplar::where('status', true)
                ->inRandomOrder()
                ->limit(random_int(1, 6))
                ->get();

            if ($exemplares->isEmpty()) {
                break;
            }

            $calculo = CalculadoraDesconto::calcular((float)$exemplares->sum('preco_venda'), $faixas);

            $venda = Venda::create([
                'id_funcionario' => $funcionarios->random()->id,
                'id_cliente' => Cliente::inRandomOrder()->value('id'),
                'data' => now()->subDays(random_int(0, 25))->setTime(random_int(9, 18), random_int(0, 59)),
                'valor_total' => $calculo['total'],
                'valor_desconto' => $calculo['valor_desconto'],
            ]);

            // A trigger do banco marca esses exemplares como vendidos
            Exemplar::whereIn('id', $exemplares->pluck('id'))->update(['id_venda' => $venda->id]);
        }
    }
}
