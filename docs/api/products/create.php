<?php
require_once __DIR__ . '/../../src/models/ProductModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['name']) || empty($input['household_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'name and household_id required']);
    exit;
}

$householdModel = new HouseholdModel($pdo);
if (!$householdModel->isMember($input['household_id'], $_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$model = new ProductModel($pdo);
$id = $model->create($input['household_id'], $input['name']);
echo json_encode(['success' => true, 'product_id' => $id]);
