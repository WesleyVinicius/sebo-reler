<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Conservacao;
use App\Models\Editora;
use App\Models\Exemplar;
use App\Models\Funcionario;
use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;
    private Funcionario $funcionario;
    private Conservacao $conservacao;

    protected function setUp(): void
    {
        parent::setUp();

        $this->funcionario = Funcionario::factory()->create();
        $this->conservacao = Conservacao::factory()->create();
    }

    private function logar(): void
    {
        $this->actingAs($this->funcionario, 'funcionario');
    }

    private function dadosValidos(array $sobrescrever = []): array
    {
        return array_merge([
            'titulo' => 'Dom Casmurro',
            'autores' => [Autor::factory()->create()->id],
            'id_editora' => Editora::factory()->create()->id,
            'id_genero' => Genero::factory()->create()->id,
            'id_conservacao' => $this->conservacao->id,
            'preco_compra' => 10,
            'preco_venda' => 25,
            'quantidade' => 3,
        ], $sobrescrever);
    }

    private function criarLivro(string $titulo = 'Dom Casmurro'): Livro
    {
        return Livro::factory()->create(['titulo' => $titulo]);
    }

    private function criarExemplares(Livro $livro, int $quantidade, float $preco = 25): void
    {
        Exemplar::factory()->count($quantidade)->create([
            'id_livro' => $livro->id,
            'id_conservacao' => $this->conservacao->id,
            'id_funcionario' => $this->funcionario->id,
            'preco_venda' => $preco,
        ]);
    }

    private function criarVendido(Livro $livro, float $preco = 25): void
    {
        Exemplar::factory()->vendido()->create([
            'id_livro' => $livro->id,
            'id_conservacao' => $this->conservacao->id,
            'id_funcionario' => $this->funcionario->id,
            'preco_venda' => $preco,
        ]);
    }

    public function test_listagem_exige_login(): void
    {
        $this->get('/livros')->assertRedirect('/login');
    }

    public function test_exemplares_iguais(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3);

        $this->get('/livros')
            ->assertStatus(200)
            ->assertViewHas('grupos', function ($grupos){
                return $grupos->total() === 1
                    && (int) $grupos->first()->quantidade === 3;
            });
    }

    public function test_exemplares_disponiveis_vendidos(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3);
        $this->criarVendido($livro);

        $this->get('/livros')
            ->assertViewHas('grupos', fn ($grupos) => $grupos->total() === 2);
    }

    public function test_busca_titulo(): void
    {
        $this->logar();
        $this->criarExemplares($this->criarLivro('Dom Casmurro'), 1);
        $this->criarExemplares($this->criarLivro('O Cortico'), 1);

        $this->get('/livros?busca=dom')
            ->assertSee('Dom Casmurro')
            ->assertDontSee('O Cortico');
    }

    // ---------- Cadastro ----------
    public function test_cadastra_livro_varios_exemplares(): void
    {
        $this->logar();

        $this->post('/livros', $this->dadosValidos(['quantidade' => 3]))
            ->assertRedirect('/livros');

        $this->assertDatabaseCount('livro', 1);
        $this->assertDatabaseCount('exemplar', 3);
        $this->assertDatabaseHas('livro', ['titulo' => 'Dom Casmurro']);
    }

    public function test_exemplares_nascem_disponiveis_e_ligados_ao_funcionario_logado(): void
    {
        $this->logar();

        $this->post('/livros', $this->dadosValidos(['quantidade' => 2]));

        $this->assertSame(2, Exemplar::where('status', true)
            ->where('id_funcionario', $this->funcionario->id)
            ->count());
    }

    public function test_cadastra_livro_varios_autores(): void
    {
        $this->logar();
        $autores = [
            Autor::create(['nome' => 'Autor A'])->id,
            Autor::create(['nome' => 'Autor B'])->id,
        ];

        $this->post('/livros', $this->dadosValidos(['autores' => $autores]));

        $this->assertCount(2, Livro::first()->autores);
    }

    public static function dadosInvalidos(): array
    {
        return [
            'sem titulo' => [['titulo' => ''], 'titulo'],
            'sem autores' => [['autores' => []], 'autores'],
            'autor inexistente' => [['autores' => [99999]], 'autores.0'],
            'editora inexistente' => [['id_editora' => 99999], 'id_editora'],
            'quantidade zero' => [['quantidade' => 0], 'quantidade'],
            'quantidade acima do limite' => [['quantidade' => 101], 'quantidade'],
            'preco negativo' => [['preco_venda' => -1], 'preco_venda'],
        ];
    }

    #[DataProvider('dadosInvalidos')]
    public function test_nao_cadastra_dados_invalidos(array $alteracao, string $campoComErro): void
    {
        $this->logar();

        $this->post('/livros', $this->dadosValidos($alteracao))
            ->assertSessionHasErrors($campoComErro);

        $this->assertDatabaseCount('livro', 0);
        $this->assertDatabaseCount('exemplar', 0);
    }

    public function test_falha_no_meio_do_cadastro_desfaz_tudo(): void
    {
        $this->logar();
        $this->withoutExceptionHandling();

        // Passa na validação, mas estoura o limite da coluna NUMERIC(10,2)
        // na hora de criar o exemplar — DEPOIS de o Livro já ter sido criado.
        $dados = $this->dadosValidos(['preco_compra' => 99999999999]);

        try {
            $this->post('/livros', $dados);
            $this->fail('Era esperado um erro do banco de dados.');
        } catch (QueryException $e) {
            // esperado
        }

        $this->assertDatabaseCount('livro', 0);
        $this->assertDatabaseCount('livro_autor', 0);
        $this->assertDatabaseCount('exemplar', 0);
    }

    // ---------- Edição ----------

    public function test_atualiza_o_grupo_inteiro(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3);
        $regular = Conservacao::firstOrCreate(['estado' => 'Regular']);
        $representante = Exemplar::orderBy('id')->first();

        $this->put(route('livros.update', $representante), $this->dadosValidos([
            'titulo' => 'Novo Titulo',
            'id_conservacao' => $regular->id,
            'preco_compra' => 15,
            'preco_venda' => 40,
        ]))->assertRedirect('/livros');

        $this->assertSame('Novo Titulo', $livro->fresh()->titulo);
        $this->assertSame(3, Exemplar::where('preco_venda', 40)
            ->where('id_conservacao', $regular->id)
            ->count());
    }

    public function test_atualizacao_substitui_a_lista_de_autores(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 1);
        $novoAutor = Autor::create(['nome' => 'Jorge Amado']);

        $this->put(route('livros.update', Exemplar::first()), $this->dadosValidos([
            'autores' => [$novoAutor->id],
        ]));

        $this->assertSame([$novoAutor->id], $livro->fresh()->autores->pluck('id')->all());
    }

    public function test_atualizacao_nao_mexe_nos_vendidos(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3, 25);
        $this->criarVendido($livro, 25);
        $representante = Exemplar::where('status', true)->orderBy('id')->first();

        $this->put(route('livros.update', $representante), $this->dadosValidos([
            'preco_venda' => 40,
        ]));

        $vendido = Exemplar::where('status', false)->first();
        $this->assertEquals(25, $vendido->preco_venda);
        $this->assertSame(3, Exemplar::where('status', true)->where('preco_venda', 40)->count());
    }

    // ---------- Exclusão ----------

    public function test_exclui_o_grupo_disponivel_e_preserva_o_vendido(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3);
        $this->criarVendido($livro);
        $representante = Exemplar::where('status', true)->orderBy('id')->first();

        $this->delete(route('livros.destroy', $representante))
            ->assertRedirect('/livros');

        $this->assertSame(0, Exemplar::where('status', true)->count());
        $this->assertSame(1, Exemplar::where('status', false)->count());
    }

    public function test_nao_exclui_exemplar_vendido(): void
    {
        $this->logar();
        $this->criarVendido($this->criarLivro());
        $vendido = Exemplar::first();

        $this->delete(route('livros.destroy', $vendido))
            ->assertRedirect('/livros')
            ->assertSessionHas('erro');

        $this->assertDatabaseCount('exemplar', 1);
    }

    public function test_excluir_todos_preserva_os_vendidos(): void
    {
        $this->logar();
        $livro = $this->criarLivro();
        $this->criarExemplares($livro, 3);
        $this->criarVendido($livro);

        $this->delete('/livros/destruir-todos')->assertRedirect('/livros');

        $this->assertSame(0, Exemplar::where('status', true)->count());
        $this->assertSame(1, Exemplar::where('status', false)->count());
    }
}
