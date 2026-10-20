<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorTest extends TestCase
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

    public function test_listagem_mostra_autores(): void
    {
        $this->logar();
        Autor::factory()->create(['nome' => 'Machado de Assis']);

        $this->get('/autores')
            ->assertStatus(200)
            ->assertSee('Machado de Assis');
    }

    public function test_busca_filtra_autor(): void
    {
        $this->logar();
        Autor::factory()->create(['nome' => 'Machado de Assis']);
        Autor::factory()->create(['nome' => 'Jorge Amado']);

        $this->get('/autores?busca=machado')
            ->assertSee('Machado de Assis')
            ->assertDontSee('Jorge Amado');
    }

    public function test_cadastrar_autor(): void
    {
        $this->logar();

        $this->post('/autores', ['nome' => 'Machado de Assis'])
            ->assertRedirect('/autores');

        $this->assertDatabaseHas('autor', ['nome' => 'Machado de Assis']);
    }

    public function test_atualiza_autor(): void
    {
        $this->logar();
        $autor = Autor::factory()->create(['nome' => 'M Assis']);

        $this->put(route('autores.update', $autor), [
            'nome' => 'Machado de Assis',
        ])->assertRedirect('/autores');

        $this->assertDatabaseHas('autor', ['id' => $autor->id, 'nome' => 'Machado de Assis']);
    }

    public function test_excluir_autor(): void
    {
        $this->logar();
        $autor = Autor::factory()->create();

        $this->delete(route('autores.destroy', $autor))
            ->assertRedirect('/autores');

        $this->assertDatabaseMissing('autor', ['id' => $autor->id]);
    }
}
