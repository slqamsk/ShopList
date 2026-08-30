<?php
require_once __DIR__ . '/../src/models/UserModel.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password required']);
    exit;
}

$userModel = new UserModel($pdo);
$user = $userModel->findByEmail($input['email']);

if (!$user || !password_verify($input['password'], $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ]
]);
