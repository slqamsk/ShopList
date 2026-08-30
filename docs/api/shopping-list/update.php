<?php
require_once __DIR__ . '/../../src/models/ShoppingListModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Item ID required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['quantity']) && !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'quantity or status required']);
    exit;
}

$model = new ShoppingListModel($pdo);

// Проверка доступа через household_id
$item = $model->getById($id);  // нужно добавить метод в модель
if (!$item) {
    http_response_code(404);
    echo json_encode(['error' => 'Item not found']);
    exit;
}

$householdModel = new HouseholdModel($pdo);
if (!$householdModel->isMember($item['household_id'], $_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if (isset($input['quantity'])) {
    $model->updateQuantity($id, $input['quantity']);
}
if (isset($input['status'])) {
    $model->updateStatus($id, $input['status']);
}

echo json_encode(['success' => true]);
