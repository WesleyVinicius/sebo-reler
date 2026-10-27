# Reler

![Testes](https://github.com/WesleyVinicius/sebo-reler/actions/workflows/testes.yml/badge.svg?branch=master)

Sistema web para gerenciamento de um sebo fictício, desenvolvido para praticar Laravel. Permite cadastrar livros com vários exemplares e vários autores, controlar o estado de conservação, registrar vendas com desconto automático por faixa de valor e emitir relatórios de vendas por período. O acesso é separado por perfil, gerente e vendedor.

## Tecnologias

- Laravel 13 com PHP 8.2 ou superior
- PostgreSQL
- Bootstrap 5 e Bootstrap Icons
- Vite, para compilação de assets
- Blade, o template engine nativo do Laravel
- PHPUnit, para testes
- GitHub Actions, para integração contínua

## Pré-requisitos

Antes de instalar, tenha certeza de que sua máquina possui:

- PHP 8.2 ou superior
- Composer
- PostgreSQL instalado e rodando
- Node.js e npm

## Instalação

```bash

# Clonar o repositório
git clone https://github.com/WesleyVinicius/sebo-reler.git
cd sebo-reler

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

Crie o banco de dados, caso ainda não exista:

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

# Terminal 1 — compila e observa mudanças em CSS e JS
npm run dev
```

```bash

# Terminal 2 — serve a aplicação
php artisan serve
```

Acesse `http://127.0.0.1:8000/login`.

Para criar o primeiro usuário de acesso, o gerente, use o Tinker:

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

## Funcionalidades

Após o login, o sistema disponibiliza:

- **Dashboard** com visão geral do sebo
- **Livros e exemplares:** cadastro de um livro com vários exemplares e vários autores, edição do grupo inteiro e exclusão que preserva os exemplares já vendidos
- **Etiquetas** para impressão de cada exemplar
- **Vendas:** busca de cliente por CPF e de exemplar, com desconto aplicado por faixa de valor
- **Clientes, autores, editoras, gêneros e estados de conservação:** CRUD com modal e exclusão individual ou em massa

Recursos exclusivos do perfil **gerente**:

- **Funcionários:** cadastro e controle de acesso
- **Descontos:** configuração das faixas de desconto
- **Relatórios de vendas** por período, com versão para impressão

O perfil **vendedor** acessa as rotas liberadas e não vê no menu os itens restritos ao gerente.

## Testes

O projeto tem 68 testes automatizados com PHPUnit, entre testes de feature e de unidade, executados a cada push e pull request pelo GitHub Actions, com PostgreSQL 16 e PHP 8.4.

```bash

php artisan test
```

O que os testes cobrem:

- **Login e autenticação:** acesso com credenciais válidas e inválidas e redirecionamento de visitantes
- **Perfis de acesso:** o que gerente, vendedor e visitante podem acessar, e itens de menu ocultos por perfil
- **Livros:** cadastro com vários exemplares e vários autores, validação de dados inválidos, atualização do grupo de exemplares, exclusão preservando exemplares vendidos, busca por título e desfazer o cadastro inteiro quando algo falha no meio
- **Clientes e autores:** operações de CRUD
- **Cálculo de desconto:** testes de unidade das faixas, inclusive sem faixas cadastradas

Ainda sem testes: vendas e relatórios.

## Estrutura do projeto

```
sebo-reler/
├── app/
│   ├── Http/Controllers/     # Livro, Venda, Cliente, Autor, Relatorio, Auth...
│   └── Models/               # Livro, Exemplar, Venda, Cliente, Desconto...
├── database/
│   └── migrations/           # Estrutura das tabelas, PostgreSQL
├── resources/
│   ├── views/                # Blade, com layout base e modais por módulo
│   ├── css/                  # CSS dividido por responsabilidade
│   └── js/app.js
├── routes/web.php
├── tests/
│   ├── Feature/              # Testes de rotas e fluxos
│   └── Unit/                 # Testes de regras isoladas
└── .github/workflows/        # Pipeline de testes no GitHub Actions
```

## Contribuindo

Este é um projeto pessoal de estudo, mas sugestões e correções são bem-vindas:

1. Faça um fork do projeto
2. Crie uma branch para sua alteração: `git checkout -b minha-alteracao`
3. Faça o commit: `git commit -m 'Descrição da alteração'`
4. Envie para a branch: `git push origin minha-alteracao`
5. Abra um Pull Request

## Licença

MIT

## Autor

**Wesley Vinicius Fernandes**

- GitHub: https://github.com/WesleyVinicius
- LinkedIn: https://linkedin.com/in/wesleyviniciusf
