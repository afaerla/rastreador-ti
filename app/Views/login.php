<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$emailValue = htmlspecialchars((string) ($email ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rastreador TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #0d6efd; }
        .login-card { max-width: 400px; width: 100%; }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card login-card shadow-lg border-0">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">
                <i class="bi bi-shield-lock"></i>
                Rastreador TI
            </h3>

            <form action="<?= htmlspecialchars($basePath) ?>/login/autenticar" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= $emailValue ?>" placeholder="Digite seu e-mail" autocomplete="username" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite sua senha" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger mt-3 mb-0" role="alert">
                        <?= htmlspecialchars((string) $erro) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>

</html>
