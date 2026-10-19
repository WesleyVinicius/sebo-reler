<?php

namespace Tests\Feature;

use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_login_carrega(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_login_credenciais_corretas(): void
    {
        $funcionario = Funcionario::factory()->create();

        $resposta = $this->post('/login', [
            'usuario' => $funcionario->usuario,
            'senha' => 'senha123',
        ]);

        $resposta->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($funcionario, 'funcionario');
    }

    public function test_login_senha_errada(): void
    {
        $funcionario = Funcionario::factory()->create();

        $resposta = $this->from('/login')->post('/login', [
            'usuario' => $funcionario->usuario,
            'senha' => 'senha-errada',
        ]);

        $resposta->assertRedirect('/login');
        $resposta->assertSessionHasErrors(['usuario']);
        $this->assertGuest('funcionario');
    }

    public function test_dashboard_exige_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_logout(): void
    {
        $funcionario = Funcionario::factory()->create();

        $this->actingAs($funcionario, 'funcionario')
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest('funcionario');
    }
}
