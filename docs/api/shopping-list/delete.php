<?php
require_once __DIR__ . '/../../src/models/ShoppingListModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Item ID required']);
    exit;
}

$model = new ShoppingListModel($pdo);
$item = $model->getById($id); // нужно добавить метод
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

$model->delete($id);
echo json_encode(['success' => true]);
