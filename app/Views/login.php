<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Rastreador TI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #0d6efd;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="card login-card shadow-lg border-0">

        <div class="card-body p-4">

            <h3 class="text-center mb-4">
                <i class="bi bi-shield-lock"></i>
                Rastreador TI
            </h3>

            <form action="/rastreador-ti/public/login/autenticar" method="POST">

                <div class="mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="nome" class="form-control" placeholder="Digite seu usuário">
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha">
                </div>

                <button class="btn btn-primary w-100">
                    Entrar
                </button>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger">
                        <?= $erro ?>
                    </div>
                <?php endif; ?>

            </form>

        </div>

    </div>

</body>

</html>