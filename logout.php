<?php
session_start();

// Destrua a sessão, o que encerrará a sessão do usuário
session_destroy();

// Redirecione para a página de login ou outra página apropriada
header("Location: connexion.php");
exit();
?>
