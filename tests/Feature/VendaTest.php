<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Desconto;
use App\Models\Exemplar;
use App\Models\Funcionario;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    private Funcionario $funcionario;
    private Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->funcionario = Funcionario::factory()->create();
        $this->cliente = Cliente::factory()->create();

        foreach ([[100, 5], [250, 10], [500, 15]] as [$minimo, $percentual]){
            Desconto::create([
                'id_funcionario' => $this->funcionario->id,
                'valor_minimo' => $minimo,
                'porcent_desconto' => $percentual,
            ]);
        }
    }

    private function logar(): void
    {
        $this->actingAs($this->funcionario, 'funcionario');
    }

    // Cria um exemplar disponível para cada preço informado
    private function exemplares(array $precos): array
    {
        return collect($precos)
            ->map(fn ($preco) => Exemplar::factory()->create(['preco_venda' => $preco]))
            ->all();
    }

    // Envia o formulário de venda com os exemplares dados
    private function vender(array $exemplares, array $alteracao = [])
    {
        return $this->post('/vendas', array_merge([
            'id_cliente' => $this->cliente->id,
            'exemplares' => collect($exemplares)->pluck('id')->all(),
        ], $alteracao));
    }

    // ---------- ACESSO ----------

    public function test_listagem_exige_login(): void
    {
        $this->get('/vendas')->assertRedirect('/login');
    }

    public function test_tela_nova_venda_vendedor(): void
    {
        $this->logar();

        $this->get('/vendas/create')
            ->assertOk()
            ->assertSee('Nova venda');
    }

    // ---------- BUSCA DE CLIENTE ----------

    public function test_busca_cliente_cpf(): void
    {
        $this->logar();
        $cpf = $this->cliente->cpf;   // só dígitos, como a factory gera
        $comMascara = sprintf('%s.%s.%s-%s',
            substr($cpf, 0, 3), substr($cpf, 3, 3), substr($cpf, 6, 3), substr($cpf, 9, 2));

        $this->get('/vendas/cliente/' . $cpf)
            ->assertOk()
            ->assertJson(['id' => $this->cliente->id, 'nome' => $this->cliente->nome]);

        $this->get('/vendas/cliente/' . $comMascara)
            ->assertOk()
            ->assertJson(['id' => $this->cliente->id]);
    }

    public function test_cpf_inexistente(): void
    {
        $this->logar();

        $this->get('/vendas/cliente/00000000000')
            ->assertNotFound()
            ->assertJson(['mensagem' => 'Cliente não encontrado.']);
    }

    // ---------- BUSCA DE EXEMPLAR ----------

    public function test_busca_exemplar_disponivel_codigo(): void
    {
        $this->logar();
        [$exemplar] = $this->exemplares([25.5]);

        $this->get('/vendas/exemplar/' . $exemplar->id)
            ->assertOk()
            ->assertJson([
                'id'     => $exemplar->id,
                'titulo' => $exemplar->livro->titulo,
                'preco'  => 25.5,
            ]);
    }

    public function test_codigo_inexistente(): void
    {
        $this->logar();

        $this->get('/vendas/exemplar/999999')
            ->assertNotFound()
            ->assertJson(['mensagem' => 'Exemplar não encontrado.']);
    }

    public function test_exemplar_vendido(): void
    {
        $this->logar();
        $vendido = Exemplar::factory()->vendido()->create();

        $this->get('/vendas/exemplar/' . $vendido->id)
            ->assertStatus(422)
            ->assertJson(['mensagem' => 'Este exemplar já foi vendido.']);
    }

    public function test_busca_exemplar_exige_login(): void
    {
        $this->get('/vendas/exemplar/1')->assertRedirect('/login');
    }

    // ---------- STORE: AS REGRAS DA VENDA ----------
    public function test_venda_exige_login(): void
    {
        $this->post('/vendas', [])->assertRedirect('/login');

        $this->assertDatabaseCount('venda', 0);
    }

    public function test_venda_sem_desconto(): void
    {
        $this->logar();
        $exemplares = $this->exemplares([30.0, 40.0]);   // subtotal 70, abaixo da 1ª faixa

        $resposta = $this->vender($exemplares);

        $venda = Venda::firstOrFail();
        $resposta->assertRedirect(route('vendas.show', $venda));

        $this->assertDatabaseHas('venda', [
            'id'             => $venda->id,
            'id_cliente'     => $this->cliente->id,
            'id_funcionario' => $this->funcionario->id,
            'valor_total'    => 70,
            'valor_desconto' => 0,
        ]);
    }

    public static function vendasComDesconto(): array
    {
        // preços dos exemplares, total, desconto
        return [
            'exatamente 100 (sem desconto)' => [[100.0], 100.0, 0.0],
            'faixa de 5%' => [[150.0], 142.5, 7.5],
            'faixa de 10%' => [[300.0], 270.0, 30.0],
            'faixa de 15%' => [[600.0], 510.0, 90.0],
            'soma de varios itens' => [[100.0, 100.0, 100.0], 270.0, 30.0],
        ];
    }

    #[DataProvider('vendasComDesconto')]
    public function test_desconto_faixa(array $precos, float $total, float $desconto): void
    {
        $this->logar();

        $this->vender($this->exemplares($precos));

        $this->assertDatabaseHas('venda', [
            'valor_total'    => $total,
            'valor_desconto' => $desconto,
        ]);
    }

    public function test_faixas_editadas_gerente(): void
    {
        $this->logar();
        // O gerente muda a faixa de R$100 de 5% para 20%
        Desconto::where('valor_minimo', 100)->update(['porcent_desconto' => 20]);

        $this->vender($this->exemplares([150.0]));

        $this->assertDatabaseHas('venda', [
            'valor_total' => 120,   // 150 - 20% (30)
            'valor_desconto' => 30,
        ]);
    }

    public function test_exemplares_vendidos_indisponiveis(): void
    {
        $this->logar();
        $exemplares = $this->exemplares([20.0, 25.0]);
        [$outro] = $this->exemplares([10.0]);   // não entra na venda

        $this->vender($exemplares);

        $venda = Venda::firstOrFail();

        foreach ($exemplares as $exemplar) {
            $atual = $exemplar->fresh();
            $this->assertEquals($venda->id, $atual->id_venda);
            $this->assertFalse((bool) $atual->status);
        }

        // quem ficou de fora continua à venda
        $this->assertNull($outro->fresh()->id_venda);
        $this->assertTrue((bool) $outro->fresh()->status);
    }

    public function test_rejeita_exemplar_vendido(): void
    {
        $this->logar();
        [$disponivel] = $this->exemplares([50.0]);
        $vendido = Exemplar::factory()->vendido()->create();
        $vendasAntes = Venda::count();   // a factory do "vendido" já cria 1 venda

        $this->vender([$disponivel, $vendido])
            ->assertSessionHasErrors('exemplares');

        $this->assertSame($vendasAntes, Venda::count());      // nenhuma venda nova
        $this->assertNull($disponivel->fresh()->id_venda);    // o disponível não foi tocado
    }

    // ---------- VALIDAÇÃO ----------
    public static function dadosInvalidos(): array
    {
        return [
            'sem cliente' => [['id_cliente' => ''], 'id_cliente'],
            'cliente inexistente' => [['id_cliente' => 999999], 'id_cliente'],
            'sem exemplares' => [['exemplares' => []], 'exemplares'],
            'codigo nao numerico' => [['exemplares' => ['abc']], 'exemplares.0'],
        ];
    }

    #[DataProvider('dadosInvalidos')]
    public function test_rejeita_dados_invalidos(array $alteracao, string $campoComErro): void
    {
        $this->logar();
        [$exemplar] = $this->exemplares([50.0]);

        $this->vender([$exemplar], $alteracao)
            ->assertSessionHasErrors($campoComErro);

        $this->assertDatabaseCount('venda', 0);
    }

    public function test_rejeita_exemplar_repetido(): void
    {
        $this->logar();
        [$exemplar] = $this->exemplares([50.0]);

        $this->vender([$exemplar, $exemplar])->assertSessionHasErrors();

        $this->assertDatabaseCount('venda', 0);
    }

    public function test_rejeita_exemplar_inexistente(): void
    {
        $this->logar();

        $this->post('/vendas', [
            'id_cliente' => $this->cliente->id,
            'exemplares' => [999999],
        ])->assertSessionHasErrors('exemplares');

        $this->assertDatabaseCount('venda', 0);
    }

    public function test_gerente_vende(): void
    {
        $gerente = Funcionario::factory()->gerente()->create();
        $this->actingAs($gerente, 'funcionario');

        $this->vender($this->exemplares([30.0]));

        $this->assertDatabaseHas('venda', ['id_funcionario' => $gerente->id]);
    }

    // ---------- TELAS ----------

    public function test_detalhe_venda(): void
    {
        $this->logar();
        [$exemplar] = $this->exemplares([150.0]);
        $this->vender([$exemplar]);
        $venda = Venda::firstOrFail();

        $this->get(route('vendas.show', $venda))
            ->assertOk()
            ->assertSee($this->cliente->nome)
            ->assertSee($exemplar->livro->titulo)
            ->assertSee('142,50') // total com 5% de desconto
            ->assertSee('7,50'); // valor do desconto
    }

    public function test_detalhe_inexistente(): void
    {
        $this->logar();

        $this->get('/vendas/999999')->assertNotFound();
    }

    public function test_listagem_vazia(): void
    {
        $this->logar();

        $this->get('/vendas')
            ->assertOk()
            ->assertSee('Nenhuma venda registrada ainda.');
    }

    public function test_listagem_venda_itens_total(): void
    {
        $this->logar();
        $this->vender($this->exemplares([100.0, 100.0]));   // 200 - 5% = 190

        $this->get('/vendas')
            ->assertOk()
            ->assertSee($this->cliente->nome)
            ->assertSee('190,00')
            ->assertViewHas('vendas', function ($vendas) {
                return $vendas->total() === 1
                    && (int) $vendas->first()->exemplares_count === 2;
            });
    }
}
