# Reler

Sistema web para gerenciamento de um sebo (livraria de livros usados), permitindo cadastrar livros, autores, editoras, clientes e registrar vendas com cálculo automático de desconto por faixa de valor.

## Tecnologias

- Laravel 13 (PHP 8.2+)
- PostgreSQL
- Bootstrap 5] + Bootstrap Icons
- Vite (compilação de assets)
- Blade (template engine nativo do Laravel)
- PHPUnit (testes)

## Pré-requisitos

Antes de instalar, tenha certeza de que sua máquina possui:

- PHP >= 8.2
- Composer
- PostgreSQL instalado e rodando
- Node.js + npm

## Instalação

```bash

# Clonar o repositório
git clone <url-do-repositorio>
cd sebo_reler2

# Instalar dependências do PHP
composer install

# Instalar dependências do front-end
npm install

# Copiar o arquivo de ambiente e configurar
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com os dados do seu banco PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sebo_reler
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

Crie o banco de dados (caso ainda não exista):

```sql
CREATE DATABASE sebo_reler;
```

Rode as migrations:

```bash

php artisan migrate
```

## Como usar

Suba os dois processos necessários, em terminais separados:

```bash

# Terminal 1 — compila e observa mudanças em CSS/JS
npm run dev
```

```bash

# Terminal 2 — serve a aplicação
php artisan serve
```

Acesse `http://127.0.0.1:8000/login`.

Para criar o primeiro usuário de acesso (gerente), use o Tinker:

```bash

php artisan tinker
```

```php
use App\Models\Funcionario;
use Illuminate\Support\Facades\Hash;

Funcionario::create([
    'usuario' => 'admin',
    'senha' => Hash::make('sua_senha'),
    'cpf' => '00000000000',
    'rg' => '000000000',
    'data_nascimento' => '1990-01-01',
    'salario' => 3000,
    'perfil' => 'GERENTE',
    'sexo' => 'Não informado',
]);
```

Após o login, o sistema disponibiliza:
- **Dashboard** com visão geral do sebo
- **Clientes**: listagem, cadastro e edição via modal, exclusão individual ou em massa
- **Gêneros**: mesmo padrão de CRUD dos clientes

## Testes

O projeto usa PHPUnit. Para rodar os testes:

```bash

php artisan test
```

> ⚠️ Este é um projeto de estudo em desenvolvimento — a suíte de testes automatizados ainda está sendo construída.

## Estrutura do projeto

```
sebo_reler2/
├── app/
│   ├── Http/Controllers/     # ClienteController, GeneroController, AuthController...
│   └── Models/               # Cliente, Genero, Livro, Venda, Funcionario...
├── database/
│   └── migrations/           # Estrutura das tabelas (Postgres)
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php   # Layout base (sidebar + header + rodapé)
│   │   ├── clientes/               # index + partials (_modal-form, _modal-scripts)
│   │   └── generos/
│   ├── css/
│   │   ├── app.css                 # Só imports
│   │   └── partials/               # variables, login, layout (CSS dividido por responsabilidade)
│   └── js/app.js             # Bootstrap + script genérico de confirmação (form-confirmar)
└── routes/web.php
```

## Contribuindo

Este é um projeto pessoal de estudo, mas sugestões e correções são bem-vindas:

1. Faça um fork do projeto
2. Crie uma branch para sua alteração (`git checkout -b minha-alteracao`)
3. Commit suas mudanças (`git commit -m 'Descrição da alteração'`)
4. Push para a branch (`git push origin minha-alteracao`)
5. Abra um Pull Request

## Licença

Este projeto não possui uma licença formal definida — uso pessoal e educacional.

## Autor

**Wesley Vinicius Fernandes**
- GitHub: https://github.com/WesleyVinicius
- LinkedIn: https://linkedin.com/in/wesleyviniciusf
