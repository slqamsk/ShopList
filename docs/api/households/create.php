<?php
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Household name required']);
    exit;
}

$model = new HouseholdModel($pdo);
try {
    $id = $model->create($input['name'], $_SESSION['user_id']);
    echo json_encode(['success' => true, 'household_id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
