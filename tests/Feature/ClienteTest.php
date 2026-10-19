<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    private function logar(): void
    {
        $this->actingAs(Funcionario::factory()->create(), 'funcionario');
    }

    public function test_listagem_exige_login(): void
    {
        $this->get('/clientes')->assertRedirect('/login');
    }

    public function test_listagem_mostra_clientes(): void
    {
        $this->logar();
        Cliente::factory()->create(['nome' => 'Maria Silva']);

        $this->get('/clientes')
            ->assertStatus(200)
            ->assertSee('Maria Silva');
    }

    public function test_busca_filtra_nome(): void
    {
        $this->logar();
        Cliente::factory()->create(['nome' => 'Maria Silva']);
        Cliente::factory()->create(['nome' => 'João Souza']);

        $this->get('/clientes?buscar=maria')
            ->assertSee('Maria Silva')
            ->assertDontSee('Joao Souza');
    }

    public function test_cadastra_cliente(): void
    {
        $this->logar();

        $this->post('/clientes', ['nome' => 'José Oliveira', 'cpf' => '12345678901'])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('cliente', ['nome' => 'José Oliveira', 'cpf' => '12345678901']);
    }

    public function test_cpf_completo(): void
    {
        $this->logar();

        $this->post('/clientes', ['nome' => 'João Ribeiro', 'cpf' => '123'])
            ->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('cliente', 0);
    }

    public function test_cpf_repetido(): void
    {
        $this->logar();
        Cliente::factory()->create(['cpf' => '12345678901']);

        $this->post('/clientes', ['nome' => 'Outro Nome', 'cpf' => '123454678901'])
            ->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('cliente', 1);
    }

    public function test_atualiza_cliente(): void
    {
        $this->logar();
        $cliente = Cliente::factory()->create(['nome' => 'Nome Antigo']);

        $this->put(route('clientes.update', $cliente), [
            'nome' => 'Nome Novo',
            'cpf' => $cliente->cpf,
        ])->assertRedirect('/clientes');

        $this->assertDatabaseHas('cliente', ['id' => $cliente->id, 'nome' => 'Nome Novo']);
    }

    public function test_exclui_cliente(): void
    {
        $this->logar();
        $cliente = Cliente::factory()->create();

        $this->delete(route('clientes.destroy', $cliente))->assertRedirect('/clientes');

        $this->assertDatabaseMissing('cliente', ['id' => $cliente->id]);
    }
}
