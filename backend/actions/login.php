<?php
/**
 * Cine Palácio - Ação de Login
 */

require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/audit.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha o e-mail e a senha.']);
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, email, password, nome FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !verifyUserPassword($password, $user['password'])) {
    // Registra falha na auditoria
    logAudit($pdo, null, $email, 'LOGIN_FALHA', 'Tentativa de login com credenciais inválidas.');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'E-mail ou senha incorretos.']);
    exit;
}

// Login com sucesso
loginUser($user);

echo json_encode([
    'success' => true,
    'message' => 'Bem-vindo ao Cine Palácio, ' . htmlspecialchars($user['nome'] ?: $user['email']) . '!',
    'redirect' => 'index.php'
]);
