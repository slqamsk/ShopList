<?php
// api/index.php - единая точка входа

session_start();

// Проверка авторизации (кроме public-эндпоинтов)
$publicEndpoints = ['login', 'register'];
$path = $_SERVER['REQUEST_URI'] ?? '';
$basePath = '/pr/api/';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Если эндпоинт не публичный и пользователь не авторизован
if (!in_array($path, $publicEndpoints) && !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

header('Content-Type: application/json');

// Маршрутизация
switch ($path) {
    case '':
        // GET /api/ - список эндпоинтов
        if ($method === 'GET') {
            echo json_encode([
                'name' => 'ShopList API',
                'endpoints' => [
                    'POST /api/register/' => 'Регистрация',
                    'POST /api/login/' => 'Логин',
                    'GET /api/user/' => 'Информация о пользователе',
                    'POST /api/households/' => 'Создать домохозяйство',
                    'GET /api/households/' => 'Список домохозяйств пользователя',
                    'GET /api/households/{id}/' => 'Информация о домохозяйстве',
                    'POST /api/households/{id}/invitations/' => 'Создать инвайт',
                    'POST /api/households/join/' => 'Принять инвайт',
                    'GET /api/products/' => 'Список продуктов текущего домохозяйства',
                    'POST /api/products/' => 'Добавить продукт',
                    'PUT /api/products/{id}/' => 'Обновить продукт',
                    'DELETE /api/products/{id}/' => 'Удалить продукт',
                    'GET /api/shopping-list/' => 'Список покупок',
                    'POST /api/shopping-list/' => 'Добавить в список покупок',
                    'PUT /api/shopping-list/{id}/' => 'Обновить количество/статус',
                    'DELETE /api/shopping-list/{id}/' => 'Удалить из списка',
                    'DELETE /api/shopping-list/clear-bought/' => 'Удалить купленные',
                ]
            ]);
        }
        break;

    case 'register':
        require __DIR__ . '/register.php';
        break;

    case 'login':
        require __DIR__ . '/login.php';
        break;

    case 'user':
        require __DIR__ . '/user.php';
        break;

    case 'households':
        if ($method === 'POST') require __DIR__ . '/households/create.php';
        elseif ($method === 'GET') require __DIR__ . '/households/list.php';
        else { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); }
        break;

    case (preg_match('/^households\/(\d+)\/$/', $path, $matches) ? true : false):
        // обрабатываем /households/{id}/
        $_GET['id'] = $matches[1];
        if ($method === 'GET') require __DIR__ . '/households/view.php';
        break;

    case (preg_match('/^households\/(\d+)\/invitations\/$/', $path, $matches) ? true : false):
        // POST /households/{id}/invitations/
        $_GET['id'] = $matches[1];
        if ($method === 'POST') require __DIR__ . '/invitations/create.php';
        break;

    case 'households/join':
        if ($method === 'POST') require __DIR__ . '/invitations/accept.php';
        break;

    case 'products':
        if ($method === 'GET') require __DIR__ . '/products/list.php';
        elseif ($method === 'POST') require __DIR__ . '/products/create.php';
        break;

    case (preg_match('/^products\/(\d+)\/$/', $path, $matches) ? true : false):
        $_GET['id'] = $matches[1];
        if ($method === 'PUT') require __DIR__ . '/products/update.php';
        elseif ($method === 'DELETE') require __DIR__ . '/products/delete.php';
        break;

    case 'shopping-list':
        if ($method === 'GET') require __DIR__ . '/shopping-list/list.php';
        elseif ($method === 'POST') require __DIR__ . '/shopping-list/add.php';
        break;

    case (preg_match('/^shopping-list\/(\d+)\/$/', $path, $matches) ? true : false):
        $_GET['id'] = $matches[1];
        if ($method === 'PUT') require __DIR__ . '/shopping-list/update.php';
        elseif ($method === 'DELETE') require __DIR__ . '/shopping-list/delete.php';
        break;

    case 'shopping-list/clear-bought':
        if ($method === 'DELETE') require __DIR__ . '/shopping-list/clear-bought.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}
