<?php

namespace Tests\Feature;

use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GerenteMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public static function rotasRestritas(): array
    {
        return [
            'funcionarios' => ['/funcionarios'],
            'descontos' => ['/descontos'],
            'relatorio de vendas' => ['/relatorios/vendas'],
            'impressao de relatorio' => ['/relatorios/vendas/imprimir'],
        ];
    }

    public static function rotasLiberadas(): array
    {
        return [
            'autores' => ['/autores'],
            'clientes' => ['/clientes'],
            'conservacoes' => ['/conservacoes'],
            'editoras' => ['/editoras'],
            'generos' => ['/generos'],
            'livros' => ['/livros'],
        ];
    }

    #[DataProvider('rotasRestritas')]
    public function test_acesso_vendedor(string $url): void
    {
        $vendedor = Funcionario::factory()->create();

        $this->actingAs($vendedor, 'funcionario')
            ->get($url)
            ->assertStatus(403);
    }

    #[DataProvider('rotasRestritas')]
    public function test_acesso_gerente(string $url): void
    {
        $gerente = Funcionario::factory()->gerente()->create();

        $this->actingAs($gerente, 'funcionario')
            ->get($url)
            ->assertStatus(200);
    }

    #[DataProvider('rotasRestritas')]
    public function test_acesso_visitante(string $url): void
    {
        $this->get($url)->assertRedirect('/login');
    }

    public function test_vendedor_cadastra_funcionario(): void
    {
        $vendedor = Funcionario::factory()->create();

        $this->actingAs($vendedor, 'funcionario')
            ->post('/funcionarios', [
                'usuario' => 'novouser',
                'senha' => '123456',
                'cpf' => '11122233344',
                'rg' => '123456789',
                'sexo' => 'Outro',
                'data_nascimento' => '1990-01-01',
                'salario' => 2000,
                'perfil' => 'GERENTE'
            ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('funcionario', ['usuario' => 'novouser']);
    }

    #[DataProvider('rotasLiberadas')]
    public function test_vendedor_acesso_rotas_liberadas(string $url): void
    {
        $vendedor = Funcionario::factory()->create();

        $this->actingAs($vendedor, 'funcionario')
            ->get($url)
            ->assertStatus(200);
    }

    public function test_menu_esconde_itens(): void
    {
        $vendedor = Funcionario::factory()->create();

        $this->actingAs($vendedor, 'funcionario')
            ->get('/dashboard')
            ->assertDontSee('Funcionários')
            ->assertDontSee('Descontos');
    }
}
