<?php
require_once __DIR__ . '/../../src/models/InvitationModel.php';
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$householdId = $_GET['id'] ?? 0;
if (!$householdId) {
    http_response_code(400);
    echo json_encode(['error' => 'Household ID required']);
    exit;
}

$householdModel = new HouseholdModel($pdo);
if (!$householdModel->isAdmin($householdId, $_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only admin can create invitations']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$inviteeEmail = $input['email'] ?? null;

$invModel = new InvitationModel($pdo);
$code = $invModel->create($householdId, $_SESSION['user_id'], $inviteeEmail);

echo json_encode(['success' => true, 'code' => $code, 'link' => "https://slqa.ru/pr/pages/join.html?code=$code"]);
