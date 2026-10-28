<?php
/**
 * Cine Palácio - Inicializador do Banco de Dados e Carga de Filmes
 */

require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/audit.php';
require_once __DIR__ . '/backend/actions/seed_filmes.php';

$pdo = getDBConnection();
$seeded = seedBrazilianMovies($pdo);

// Cria um usuário demonstrativo caso não haja usuários cadastrados
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = (int)$stmt->fetchColumn();

if ($userCount === 0) {
    $demoEmail = 'cinefilo@cinepalacio.br';
    $demoPassword = hashUserPassword('cinema123'); // Bcrypt
    $stmtUser = $pdo->prepare("
        INSERT INTO users (email, password, nome)
        VALUES (:email, :password, :nome)
    ");
    $stmtUser->execute([
        ':email' => $demoEmail,
        ':password' => $demoPassword,
        ':nome' => 'Cinéfilo Paulistano'
    ]);
    $userId = (int)$pdo->lastInsertId();
    logAudit($pdo, $userId, $demoEmail, 'CADASTRO', 'Usuário demonstrativo inicial criado com senha bcrypt.');
}

// Se executado via CLI
if (php_sapi_name() === 'cli') {
    echo "=== Cine Palácio - Inicialização Concluída ===\n";
    echo "Filmes semeados: {$seeded}\n";
    echo "Banco de dados configurado com segurança PDO e auditoria ativa.\n";
    exit(0);
}

// Se acessado via Navegador
header('Location: index.php?msg=initialized');
exit;
