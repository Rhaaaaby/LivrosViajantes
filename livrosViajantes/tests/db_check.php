<?php
require_once __DIR__ . '/../app/bootstrap.php';

$class = 'App\\Database\\Connection';
$db = $class::connect();
$result = $db->query('SELECT 1 AS ok')->fetch();
echo json_encode($result, JSON_UNESCAPED_UNICODE);
