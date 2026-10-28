<?php
/**
 * Cine Palácio - Ação de Redefinição de Senha
 * Atualiza senha de forma segura com Bcrypt e registra log de auditoria.
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

$email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor, informe um e-mail válido.']);
    exit;
}

if (empty($newPassword) || strlen($newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A nova senha deve ter no mínimo 6 caracteres.']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A confirmação de senha não confere.']);
    exit;
}

$pdo = getDBConnection();

// Busca o usuário
$stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    // Por segurança e clareza, informamos
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Não encontramos nenhuma conta com este e-mail no Cine Palácio.']);
    exit;
}

// Gera novo hash Bcrypt
$newHash = hashUserPassword($newPassword);

// Atualiza a senha no banco
$stmtUpdate = $pdo->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");
$stmtUpdate->execute([
    ':password' => $newHash,
    ':id' => $user['id']
]);

// Registra log de auditoria
logAudit($pdo, (int)$user['id'], $user['email'], 'REDEFINICAO_SENHA', 'Senha de acesso redefinida com sucesso.');

echo json_encode([
    'success' => true,
    'message' => 'Sua senha foi redefinida com sucesso! Você já pode entrar com a nova senha.',
    'redirect' => 'login.php'
]);
