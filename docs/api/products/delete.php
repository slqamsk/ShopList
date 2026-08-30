<?php
require_once __DIR__ . '/../../src/models/ProductModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

$productModel = new ProductModel($pdo);
$product = $productModel->getById($id);
if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$householdModel = new HouseholdModel($pdo);
if (!$householdModel->isMember($product['household_id'], $_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$productModel->delete($id);
echo json_encode(['success' => true]);
