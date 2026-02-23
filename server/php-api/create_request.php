<?php
header('Content-Type: application/json; charset=utf-8');

// Usar PDO desde db.php
$pdo = require __DIR__ . '/db.php';

try {
    // Soportar JSON o form-encoded
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if ($data === null) {
        parse_str($raw, $data);
    }

    // Si no hay body y hay POST tradicional, usar $_POST
    if (!$data || !is_array($data)) {
        $data = $_POST;
    }

    // Validaciones mínimas
    $employeeId = isset($data['employee_id']) ? (int)$data['employee_id'] : null;
    $dateRequested = isset($data['date_requested']) ? trim($data['date_requested']) : null;
    $days = isset($data['days']) ? (int)$data['days'] : 1;
    $notes = isset($data['notes']) ? trim($data['notes']) : null;

    if (empty($employeeId) || empty($dateRequested)) {
        http_response_code(400);
        echo json_encode(['error' => 'employee_id y date_requested son requeridos']);
        exit;
    }

    // Validar formato de fecha YYYY-MM-DD
    $d = DateTime::createFromFormat('Y-m-d', $dateRequested);
    if (!$d || $d->format('Y-m-d') !== $dateRequested) {
        http_response_code(400);
        echo json_encode(['error' => 'date_requested debe tener formato YYYY-MM-DD']);
        exit;
    }

    // Normalizar días y límites básicos
    if ($days < 1)
        $days = 1;
    if ($days > 30)
        $days = 30; // prevención arbitraria

    // Insertar solicitud como 'pendiente'
    // Insertar solicitud como 'pendiente'
    $stmt = $pdo->prepare('INSERT INTO requests (employee_id, date_requested, days, status, notes) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$employeeId, $dateRequested, $days, 'pendiente', $notes]);
    $id = $pdo->lastInsertId();

    // Recuperar la fila creada
    $sel = $pdo->prepare('SELECT * FROM requests WHERE id = ?');
    $sel->execute([$id]);
    $row = $sel->fetch();

    http_response_code(201);
    echo json_encode($row ?: ['id' => $id]);
    exit;
}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear la solicitud', 'message' => $e->getMessage()]);
    exit;
}
