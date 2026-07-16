<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Reler')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">

<div class="app-shell">

    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo_reler.png') }}" alt="Reler">
            <span class="brand-name">RELER</span>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house nav-icon"></i> Dashboard
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-book nav-icon"></i> Livros
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-pen nav-icon"></i> Autores
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-bank nav-icon"></i> Editoras
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-tag nav-icon"></i> Gêneros
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-check-circle nav-icon"></i> Estado de Conservação
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-cart nav-icon"></i> Vendas
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-people nav-icon"></i> Clientes
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-bar-chart nav-icon"></i> Relatórios
            </a>
            <a href="#" class="nav-item">
                <i class="bi bi-gear nav-icon"></i> Configurações
            </a>
        </nav>
    </aside>

    <div class="app-main">
        <header class="app-header">
            <div>
                <h1 class="page-title">@yield('titulo', 'Dashboard')</h1>
                <p class="page-subtitle">@yield('subtitulo', '')</p>
            </div>

            <div class="header-user">
                @php $funcionario = Auth::guard('funcionario')->user(); @endphp
                <div class="user-avatar"><i class="bi bi-person-circle"></i></div>
                <div>
                    <div class="user-name">{{ $funcionario->usuario }}</div>
                    <span class="user-badge">{{ $funcionario->perfil }}</span>
                </div>

                <form method="POST" action="{{ url('/logout') }}" class="ms-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </button>
                </form>
            </div>
        </header>

        <main class="app-content">
            @yield('conteudo')
        </main>
    </div>

</div>

</body>
</html>
