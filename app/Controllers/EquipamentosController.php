<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EquipamentosModel;
use App\Models\EquipamentosRepository;
use Core\Controller;
use DateTimeImmutable;
use PDOException;

class EquipamentosController extends Controller
{
    private EquipamentosRepository $repository;

    public function __construct()
    {
        $this->repository = new EquipamentosRepository();
    }

    public function index(): void
    {
        $this->view('equipamentos', [
            'equipamentos' => $this->repository->select(),
            'mensagem' => $this->feedbackMessage((string) ($_GET['resultado'] ?? '')),
            'mensagemErro' => isset($_GET['erro']),
        ]);
    }

    public function create(array $errors = [], array $old = [], ?int $equipmentId = null): void
    {
        $this->view('equipamentos-form', [
            'categorias' => $this->repository->selectCategories(),
            'errors' => $errors,
            'old' => $old,
            'equipmentId' => $equipmentId,
        ]);
    }

    public function store(): void
    {
        $input = $this->input();

        $errors = $this->validate($input);

        if ($errors !== []) {
            http_response_code(422);
            $this->create($errors, $input);
            return;
        }

        $this->fillModel(new EquipamentosModel(), $input)->save();

        $this->redirect('home/equipamentos?resultado=cadastrado');
    }

    public function edit(): void
    {
        $id = $this->validId($_GET['id'] ?? null);
        $equipment = $id === null ? null : $this->repository->find($id);
        if ($equipment === null) {
            $this->redirect('home/equipamentos?erro=nao-encontrado');
        }

        $old = array_map(static fn ($value): string => (string) ($value ?? ''), $equipment);
        $this->create([], $old, $id);
    }

    public function update(): void
    {
        $id = $this->validId($_POST['id'] ?? null);
        if ($id === null || $this->repository->find($id) === null) {
            $this->redirect('home/equipamentos?erro=nao-encontrado');
        }

        $input = $this->input();
        $errors = $this->validate($input, $id);
        if ($errors !== []) {
            http_response_code(422);
            $this->create($errors, $input, $id);
            return;
        }

        $model = new EquipamentosModel();
        $model->id = $id;
        $this->fillModel($model, $input)->save();
        $this->redirect('home/equipamentos?resultado=atualizado');
    }

    public function destroy(): void
    {
        $id = $this->validId($_POST['id'] ?? null);
        if ($id === null) {
            $this->redirect('home/equipamentos?erro=nao-encontrado');
        }

        try {
            $deleted = $this->repository->delete($id);
            $this->redirect('home/equipamentos?' . ($deleted ? 'resultado=excluido' : 'erro=nao-encontrado'));
        } catch (PDOException) {
            $this->redirect('home/equipamentos?erro=vinculado');
        }
    }

    /**
     * @param array<string, string> $input
     * @return array<string, string>
     */
    private function validate(array $input, ?int $ignoredId = null): array
    {
        $errors = [];

        if ($input['nome'] === '') {
            $errors['nome'] = 'Informe o nome do equipamento.';
        } elseif (mb_strlen($input['nome']) > 150) {
            $errors['nome'] = 'O nome deve ter no máximo 150 caracteres.';
        }

        if (mb_strlen($input['marca']) > 100) {
            $errors['marca'] = 'A marca deve ter no máximo 100 caracteres.';
        }

        if (mb_strlen($input['modelo']) > 100) {
            $errors['modelo'] = 'O modelo deve ter no máximo 100 caracteres.';
        }

        if ($input['numero_serie'] === '') {
            $errors['numero_serie'] = 'Informe o número de série.';
        } elseif (mb_strlen($input['numero_serie']) > 100) {
            $errors['numero_serie'] = 'O número de série deve ter no máximo 100 caracteres.';
        } elseif ($this->repository->serialNumberExists($input['numero_serie'], $ignoredId)) {
            $errors['numero_serie'] = 'Este número de série já está cadastrado.';
        }

        $categoryId = filter_var($input['categoria_id'], FILTER_VALIDATE_INT);
        if ($categoryId === false || !$this->repository->categoryExists((int) $categoryId)) {
            $errors['categoria_id'] = 'Selecione uma categoria válida.';
        }

        if ($input['data_aquisicao'] !== '' && !$this->isValidDate($input['data_aquisicao'])) {
            $errors['data_aquisicao'] = 'Informe uma data de aquisição válida.';
        }

        if (!in_array($input['status'], ['disponivel', 'em_uso', 'manutencao', 'baixado'], true)) {
            $errors['status'] = 'Selecione um status válido.';
        }

        if (mb_strlen($input['observacoes']) > 5000) {
            $errors['observacoes'] = 'As observações devem ter no máximo 5000 caracteres.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private function input(): array
    {
        $fields = ['nome', 'marca', 'modelo', 'numero_serie', 'categoria_id', 'status', 'data_aquisicao', 'observacoes'];
        $input = [];
        foreach ($fields as $field) {
            $input[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $input['status'] = $input['status'] !== '' ? $input['status'] : 'disponivel';
        return $input;
    }

    /** @param array<string, string> $input */
    private function fillModel(EquipamentosModel $model, array $input): EquipamentosModel
    {
        $model->nome = $input['nome'];
        $model->marca = $input['marca'] !== '' ? $input['marca'] : null;
        $model->modelo = $input['modelo'] !== '' ? $input['modelo'] : null;
        $model->numeroSerie = $input['numero_serie'];
        $model->categoriaId = (int) $input['categoria_id'];
        $model->status = $input['status'];
        $model->dataAquisicao = $input['data_aquisicao'] !== '' ? $input['data_aquisicao'] : null;
        $model->observacoes = $input['observacoes'] !== '' ? $input['observacoes'] : null;
        return $model;
    }

    private function validId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $id === false ? null : (int) $id;
    }

    private function feedbackMessage(string $result): ?string
    {
        return match ($result) {
            'cadastrado' => 'Equipamento cadastrado com sucesso.',
            'atualizado' => 'Equipamento atualizado com sucesso.',
            'excluido' => 'Equipamento excluído com sucesso.',
            default => isset($_GET['erro']) ? match ((string) $_GET['erro']) {
                'vinculado' => 'O equipamento possui registros vinculados e não pode ser excluído.',
                default => 'Equipamento não encontrado.',
            } : null,
        };
    }

    private function isValidDate(string $date): bool
    {
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
    }
}
