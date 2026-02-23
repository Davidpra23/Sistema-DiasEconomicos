<?php
header('Content-Type: application/json; charset=utf-8');
$pdo = require __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Listar departamentos
        $stmt = $pdo->query('SELECT id, name FROM departments ORDER BY name');
        $rows = $stmt->fetchAll();
        echo json_encode($rows);
        exit;
    }

    if ($method === 'POST') {
        // Crear departamento
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name es requerido']);
            exit;
        }

        $name = trim($data['name']);

        // Verificar si ya existe un departamento con ese nombre
        $check = $pdo->prepare('SELECT id FROM departments WHERE name = ?');
        $check->execute([$name]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Ya existe un departamento con ese nombre']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO departments (name) VALUES (?)');
        $stmt->execute([$name]);
        $id = $pdo->lastInsertId();

        $created = $pdo->prepare('SELECT id, name FROM departments WHERE id = ?');
        $created->execute([$id]);
        echo json_encode($created->fetch());
        exit;
    }

    if ($method === 'PUT') {
        // Actualizar departamento: /departments.php?id=123
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id es requerido en la query string']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name es requerido']);
            exit;
        }

        $name = trim($data['name']);

        // Verificar si ya existe otro departamento con ese nombre
        $check = $pdo->prepare('SELECT id FROM departments WHERE name = ? AND id != ?');
        $check->execute([$name, $id]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Ya existe otro departamento con ese nombre']);
            exit;
        }

        $stmt = $pdo->prepare('UPDATE departments SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);

        $updated = $pdo->prepare('SELECT id, name FROM departments WHERE id = ?');
        $updated->execute([$id]);
        echo json_encode($updated->fetch());
        exit;
    }

    if ($method === 'DELETE') {
        // Eliminar departamento: /departments.php?id=123
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id es requerido en la query string']);
            exit;
        }

        // Verificar si tiene empleados asociados
        $check = $pdo->prepare('SELECT COUNT(*) FROM employees WHERE department_id = ?');
        $check->execute([$id]);
        $count = $check->fetchColumn();

        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar el departamento porque tiene empleados asociados']);
            exit;
        }

        $stmt = $pdo->prepare('DELETE FROM departments WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Método no soportado']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en departments endpoint', 'message' => $e->getMessage()]);
}

