<?php
/**
 * Cine Palácio - Ação de Logout
 */

require_once __DIR__ . '/../../config/auth.php';

logoutUser();

// Redireciona para home com aviso
header('Location: ../../index.php?msg=logged_out');
exit;
