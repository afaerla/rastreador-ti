<?php

declare(strict_types=1);

namespace Core;

use App\Controllers\HomeController;
use App\Controllers\EquipamentosController;
use App\Controllers\LoginController;

class Router
{
    private const PUBLIC_ROUTES = ['login', 'login/autenticar'];

    /** @var array<string, array<string, array{class-string, string}>> */
    private const ROUTES = [
        'GET' => [
            'login' => [LoginController::class, 'index'],
            'home' => [HomeController::class, 'index'],
            'home/equipamentos' => [EquipamentosController::class, 'index'],
            'home/equipamentos/novo' => [EquipamentosController::class, 'create'],
            'home/equipamentos/editar' => [EquipamentosController::class, 'edit'],
            'home/categorias' => [HomeController::class, 'categorias'],
            'home/emprestimos' => [HomeController::class, 'emprestimos'],
            'home/manutencoes' => [HomeController::class, 'manutencoes'],
            'home/usuarios' => [HomeController::class, 'usuarios'],
        ],
        'POST' => [
            'home/equipamentos' => [EquipamentosController::class, 'store'],
            'home/equipamentos/atualizar' => [EquipamentosController::class, 'update'],
            'home/equipamentos/excluir' => [EquipamentosController::class, 'destroy'],
            'login/autenticar' => [LoginController::class, 'autenticar'],
            'logout' => [LoginController::class, 'logout'],
        ],
    ];

    public function run(): void
    {
        $route = trim((string) ($_GET['rota'] ?? 'login'), '/');
        $route = $route === '' ? 'login' : $route;
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if (!in_array($route, self::PUBLIC_ROUTES, true) && !isset($_SESSION['usuario_id'])) {
            $this->redirectToLogin();
            return;
        }

        if (!isset(self::ROUTES[$method][$route])) {
            if ($this->routeExistsForAnotherMethod($route)) {
                $this->methodNotAllowed();
                return;
            }

            $this->notFound();
            return;
        }

        [$controllerClass, $action] = self::ROUTES[$method][$route];
        $controller = new $controllerClass();
        $controller->{$action}();
    }

    private function redirectToLogin(): void
    {
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        header('Location: ' . $basePath . '/login');
    }

    private function routeExistsForAnotherMethod(string $route): bool
    {
        foreach (self::ROUTES as $routes) {
            if (isset($routes[$route])) {
                return true;
            }
        }

        return false;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>Erro 404</h1>';
        echo '<p>Página não encontrada.</p>';
    }

    private function methodNotAllowed(): void
    {
        http_response_code(405);
        echo '<h1>Erro 405</h1>';
        echo '<p>Método não permitido para esta rota.</p>';
    }
}
