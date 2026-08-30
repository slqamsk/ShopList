<?php
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Household ID required']);
    exit;
}

$model = new HouseholdModel($pdo);
$household = $model->getById($id, $_SESSION['user_id']);

if (!$household) {
    http_response_code(404);
    echo json_encode(['error' => 'Household not found']);
    exit;
}

$members = $model->getMembers($id);
echo json_encode(['success' => true, 'household' => $household, 'members' => $members]);
