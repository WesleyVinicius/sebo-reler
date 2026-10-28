<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Desconto;
use App\Models\Exemplar;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
