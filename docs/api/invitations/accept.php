<?php
require_once __DIR__ . '/../../src/models/InvitationModel.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invitation code required']);
    exit;
}

$invModel = new InvitationModel($pdo);
$inv = $invModel->findByCode($input['code']);

if (!$inv) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid or expired invitation']);
    exit;
}

try {
    $invModel->accept($input['code'], $_SESSION['user_id']);
    echo json_encode(['success' => true, 'household_id' => $inv['household_id']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
