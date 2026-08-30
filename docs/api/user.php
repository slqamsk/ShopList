<?php
require_once __DIR__ . '/../src/models/UserModel.php';

$userModel = new UserModel($pdo);
$user = $userModel->findById($_SESSION['user_id']);

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

echo json_encode(['success' => true, 'user' => $user]);
