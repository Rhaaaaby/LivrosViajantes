<?php
require_once __DIR__ . '/../app/bootstrap.php';
$class = 'App\\Database\\Connection';
$db = $class::connect();

$tables = ['usuario', 'livro', 'solicitacao', 'mensagem'];
foreach ($tables as $table) {
    $stmt = $db->prepare("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = :table ORDER BY ordinal_position");
    $stmt->execute([':table' => $table]);
    $cols = $stmt->fetchAll();
    echo "TABLE: $table\n";
    foreach ($cols as $col) {
        echo "  {$col['column_name']} ({$col['data_type']})\n";
    }
    echo "\n";
}
