<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Exemplar;
use App\Models\Funcionario;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RelatorioTest extends TestCase
{
    use RefreshDatabase;

    private Funcionario $gerente;
    private Funcionario $vendedor;
    private Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gerente  = Funcionario::factory()->gerente()->create();
        $this->vendedor = Funcionario::factory()->create();
        $this->cliente  = Cliente::factory()->create();
    }

    private function logarComoGerente(): void
    {
        $this->actingAs($this->gerente, 'funcionario');
    }

    /**
     * Cria uma venda numa data específica, já com os exemplares vendidos.
     */
    private function venda(
        string $data,
        float $total,
        float $desconto = 0,
        int $itens = 1,
        ?Funcionario $funcionario = null
    ): Venda {
        $venda = Venda::create([
            'id_funcionario' => ($funcionario ?? $this->gerente)->id,
            'id_cliente'     => $this->cliente->id,
            'data'           => $data,
            'valor_total'    => $total,
            'valor_desconto' => $desconto,
        ]);

        Exemplar::factory()->count($itens)->create([
            'id_venda' => $venda->id,
            'status'   => false,
        ]);

        return $venda;
    }

    // ---------- ACESSO ----------

    public static function rotasDoRelatorio(): array
    {
        return [
            'tela'      => ['/relatorios/vendas'],
            'impressao' => ['/relatorios/vendas/imprimir'],
        ];
    }

    #[DataProvider('rotasDoRelatorio')]
    public function test_exige_login(string $rota): void
    {
        $this->get($rota)->assertRedirect('/login');
    }

    #[DataProvider('rotasDoRelatorio')]
    public function test_vendedor_nao_acessa(string $rota): void
    {
        $this->actingAs($this->vendedor, 'funcionario');

        $this->get($rota)->assertForbidden();
    }

    #[DataProvider('rotasDoRelatorio')]
    public function test_gerente_acessa(string $rota): void
    {
        $this->logarComoGerente();

        $this->get($rota)->assertOk();
    }

    // ---------- PERÍODO ----------
    public function test_periodo_padrao_mes_atual(): void
    {
        $this->logarComoGerente();
        $this->venda(now()->format('Y-m-d H:i:s'), 100);
        $this->venda(now()->subMonths(2)->format('Y-m-d H:i:s'), 900);

        $this->get('/relatorios/vendas')
            ->assertOk()
            ->assertViewHas('resumo', fn ($r) => $r['quantidade'] === 1 && $r['liquido'] === 100.0);
    }

    public function test_filtro_inclui_o_primeiro_e_o_ultimo_instante(): void
    {
        $this->logarComoGerente();
        $this->venda('2026-02-28 23:59:59', 10);   // fora: um segundo antes
        $this->venda('2026-03-01 00:00:00', 20);   // dentro: primeiro instante
        $this->venda('2026-03-31 23:59:59', 30);   // dentro: último instante
        $this->venda('2026-04-01 00:00:00', 40);   // fora: um instante depois

        $this->get('/relatorios/vendas?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertViewHas('resumo', fn ($r) => $r['quantidade'] === 2 && $r['liquido'] === 50.0);
    }

    public function test_periodo_sem_vendas(): void
    {
        $this->logarComoGerente();
        $this->venda('2026-01-10 10:00:00', 100);

        $this->get('/relatorios/vendas?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertOk()
            ->assertSee('Nenhuma venda encontrada neste período.')
            ->assertViewHas('resumo', fn ($r) => $r['quantidade'] === 0 && $r['liquido'] === 0.0);
    }

    // ---------- TOTAIS ----------

    public function test_resumo_soma_vendas_itens_e_descontos(): void
    {
        $this->logarComoGerente();
        $this->venda('2026-03-10 10:00:00', 190, 10, 2);   // 200 - 10
        $this->venda('2026-03-12 10:00:00', 300, 0, 3);

        $this->get('/relatorios/vendas?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertViewHas('resumo', [
                'quantidade' => 2,
                'itens'      => 5,
                'bruto'      => 500.0,
                'descontos'  => 10.0,
                'liquido'    => 490.0,
            ]);
    }

    public function test_total_por_vendedor(): void
    {
        $this->logarComoGerente();
        $this->venda('2026-03-10 10:00:00', 300);                                    // gerente
        $this->venda('2026-03-11 10:00:00', 100, 0, 1, $this->vendedor);             // vendedor
        $this->venda('2026-03-12 10:00:00', 50, 0, 1, $this->vendedor);              // vendedor

        $this->get('/relatorios/vendas?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertViewHas('porVendedor', function ($lista) {
                return $lista->count() === 2
                    && $lista->first()->id_funcionario == $this->gerente->id     // maior total primeiro
                    && (int) $lista->first()->quantidade === 1
                    && (float) $lista->last()->total === 150.0;
            });
    }

    // ---------- VALIDAÇÃO ----------

    public static function periodosInvalidos(): array
    {
        return [
            'final antes da inicial' => ['?data_inicio=2026-03-10&data_fim=2026-03-01', 'data_fim'],
            'inicial invalida'       => ['?data_inicio=abc',                            'data_inicio'],
            'final invalida'         => ['?data_fim=xyz',                               'data_fim'],
        ];
    }

    #[DataProvider('periodosInvalidos')]
    public function test_rejeita_periodo_invalido(string $query, string $campoComErro): void
    {
        $this->logarComoGerente();

        $this->get('/relatorios/vendas' . $query)
            ->assertSessionHasErrors($campoComErro);
    }

    // ---------- TELA E IMPRESSÃO ----------

    /** 16 vendas em março: uma a mais que a página de 15 */
    private function dezesseisVendasEmMarco(): void
    {
        for ($dia = 1; $dia <= 16; $dia++) {
            $this->venda(sprintf('2026-03-%02d 10:00:00', $dia), 10);
        }
    }

    public function test_tela_pagina_e_mantem_o_filtro_nos_links(): void
    {
        $this->logarComoGerente();
        $this->dezesseisVendasEmMarco();

        $this->get('/relatorios/vendas?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertOk()
            ->assertViewHas('vendas', fn ($v) => $v->count() === 15 && $v->total() === 16)
            ->assertSee('data_inicio=2026-03-01', false);   // o link da página 2 leva o filtro
    }

    public function test_impressao_lista_tudo_sem_paginar(): void
    {
        $this->logarComoGerente();
        $this->dezesseisVendasEmMarco();

        $this->get('/relatorios/vendas/imprimir?data_inicio=2026-03-01&data_fim=2026-03-31')
            ->assertOk()
            ->assertSee('Relatório de vendas')
            ->assertSee('Gerado por ' . $this->gerente->usuario)
            ->assertViewHas('vendas', fn ($v) => $v->count() === 16
                && $v->first()->data->lte($v->last()->data));   // ordem crescente de data
    }
}
