<?php
require_once __DIR__ . '/../src/models/UserModel.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['username'], $input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username, email, password required']);
    exit;
}

$userModel = new UserModel($pdo);

if ($userModel->findByEmail($input['email'])) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit;
}

try {
    $id = $userModel->create($input['username'], $input['email'], $input['password']);
    echo json_encode(['success' => true, 'user_id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
