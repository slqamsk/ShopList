<?php
require_once __DIR__ . '/../../src/models/HouseholdModel.php';

$model = new HouseholdModel($pdo);
$list = $model->getByUser($_SESSION['user_id']);
echo json_encode(['success' => true, 'households' => $list]);
