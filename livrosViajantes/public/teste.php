<?php
echo "<h1>Servidor Funcionando!</h1>";
echo "<p>Arquivos verificados:</p>";
$files = ['pages/area_usuario.html', 'pages/publicacao_detalhada.html'];
foreach ($files as $file) {
    echo "<p>" . (file_exists($file) ? "✓" : "✗") . " $file</p>";
}
?>