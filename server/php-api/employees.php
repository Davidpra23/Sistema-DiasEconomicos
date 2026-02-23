<?php
header('Content-Type: application/json; charset=utf-8');
$pdo = require __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    // Si se solicita lista de departamentos
    if (isset($_GET['departments'])) {
        $stmt = $pdo->query('SELECT id, name FROM departments ORDER BY name');
        $rows = $stmt->fetchAll();
        echo json_encode($rows);
        exit;
    }

    if ($method === 'GET') {
        // Lista de empleados con nombre de departamento
        $sql = "SELECT e.id, e.name, e.department_id, d.name AS department,
            0 AS initial_days_month,
            0 AS initial_days_year
            FROM employees e
            LEFT JOIN departments d ON d.id = e.department_id
            ORDER BY e.name";

        $stmt = $pdo->query($sql);
        $employees = $stmt->fetchAll();

        // Obtener todas las solicitudes aprobadas para calcular uso dinámicamente
        $reqStmt = $pdo->query("SELECT employee_id, date_requested, days FROM requests WHERE status = 'aprobada'");
        $requests = $reqStmt->fetchAll();

        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');
        $usage = [];

        foreach ($requests as $req) {
            $empId = $req['employee_id'];
            $start = new DateTime($req['date_requested']);
            $days = (int)$req['days'];

            for ($i = 0; $i < $days; $i++) {
                $dayIter = clone $start;
                $dayIter->modify("+$i days");
                $m = (int)$dayIter->format('m');
                $y = (int)$dayIter->format('Y');

                if (!isset($usage[$empId])) {
                    $usage[$empId] = ['month' => 0, 'year' => 0];
                }

                if ($y === $currentYear) {
                    $usage[$empId]['year']++;
                    if ($m === $currentMonth) {
                        $usage[$empId]['month']++;
                    }
                }
            }
        }

        // Asignar valores calculados
        foreach ($employees as &$emp) {
            $id = $emp['id'];
            $emp['days_used_month'] = $usage[$id]['month'] ?? 0;
            $emp['days_used_year'] = $usage[$id]['year'] ?? 0;
            // Mantener compatibilidad con frontend que espera estos campos
            $emp['initial_days_month'] = $emp['days_used_month'];
            $emp['initial_days_year'] = $emp['days_used_year'];
        }
        unset($emp);

        echo json_encode($employees);
        exit;
    }

    if ($method === 'POST') {
        // Crear empleado
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name es requerido']);
            exit;
        }

        $name = trim($data['name']);
        $departmentId = isset($data['department_id']) && $data['department_id'] !== '' ? (int)$data['department_id'] : null;
        $initialDaysMonth = isset($data['initial_days_month']) ? (int)$data['initial_days_month'] : 0;
        $initialDaysYear = isset($data['initial_days_year']) ? (int)$data['initial_days_year'] : 0;

        $stmt = $pdo->prepare('INSERT INTO employees (name, department_id, initial_days_month, initial_days_year) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $departmentId, $initialDaysMonth, $initialDaysYear]);
        $id = $pdo->lastInsertId();

        $created = $pdo->prepare('SELECT e.id, e.name, e.department_id, d.name AS department, e.initial_days_month, e.initial_days_year FROM employees e LEFT JOIN departments d ON d.id = e.department_id WHERE e.id = ?');
        $created->execute([$id]);
        echo json_encode($created->fetch());
        exit;
    }

    if ($method === 'PUT') {
        // Actualizar empleado: /employees.php?id=123
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
        $departmentId = isset($data['department_id']) && $data['department_id'] !== '' ? (int)$data['department_id'] : null;
        $initialDaysMonth = isset($data['initial_days_month']) ? (int)$data['initial_days_month'] : 0;
        $initialDaysYear = isset($data['initial_days_year']) ? (int)$data['initial_days_year'] : 0;

        $stmt = $pdo->prepare('UPDATE employees SET name = ?, department_id = ?, initial_days_month = ?, initial_days_year = ? WHERE id = ?');
        $stmt->execute([$name, $departmentId, $initialDaysMonth, $initialDaysYear, $id]);

        $updated = $pdo->prepare('SELECT e.id, e.name, e.department_id, d.name AS department, e.initial_days_month, e.initial_days_year FROM employees e LEFT JOIN departments d ON d.id = e.department_id WHERE e.id = ?');
        $updated->execute([$id]);
        echo json_encode($updated->fetch());
        exit;
    }

    if ($method === 'DELETE') {
        // Eliminar empleado: /employees.php?id=123
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'id es requerido en la query string']);
            exit;
        }

        // Verificar si tiene solicitudes asociadas
        $check = $pdo->prepare('SELECT COUNT(*) FROM requests WHERE employee_id = ?');
        $check->execute([$id]);
        $count = $check->fetchColumn();

        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar el empleado porque tiene solicitudes asociadas']);
            exit;
        }

        $stmt = $pdo->prepare('DELETE FROM employees WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Método no soportado']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en employees endpoint', 'message' => $e->getMessage()]);
}
