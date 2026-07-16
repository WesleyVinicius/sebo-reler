<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reler</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">

<div class="reler-card">
    <div class="reler-brand">
        <img src="{{ asset('images/logo_reler.png') }}" alt="Reler">
        <div class="tagline">Seu sebo, sua história.</div>
    </div>

    <div class="reler-form">
        <h2>Bem-vindo!</h2>
        <p class="subtitle">Faça login para acessar o sistema.</p>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuário</label>
                <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2 1H6a4 4 0 0 0-4 4v1h12v-1a4 4 0 0 0-4-4Z"/>
                            </svg>
                        </span>
                    <input type="text" class="form-control" id="usuario" name="usuario"
                           placeholder="Digite seu usuário" value="{{ old('usuario') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2Zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                            </svg>
                        </span>
                    <input type="password" class="form-control" id="senha" name="senha"
                           placeholder="Digite sua senha" required>
                </div>
            </div>

            <button type="submit" class="btn btn-reler w-100 mt-2">Entrar</button>
        </form>
    </div>
</div>

<div class="reler-footer">
    &copy; {{ date('Y') }} Reler. Todos os direitos reservados.
</div>

</body>
</html>
