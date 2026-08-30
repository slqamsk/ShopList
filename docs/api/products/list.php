<?php
require_once __DIR__ . '/../../src/models/ProductModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$input = json_decode(file_get_contents('php://input'), true);
$householdId = $input['household_id'] ?? null;

if (!$householdId) {
    http_response_code(400);
    echo json_encode(['error' => 'household_id required']);
    exit;
}

$householdModel = new HouseholdModel($pdo);
if (!$householdModel->isMember($householdId, $_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$model = new ProductModel($pdo);
$products = $model->getByHousehold($householdId);
echo json_encode(['success' => true, 'products' => $products]);
