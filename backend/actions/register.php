<?php
/**
 * Cine Palácio - Ação de Cadastro
 * Cadastro simples com Email e Senha, criptografia Bcrypt e registro de auditoria.
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
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Validações básicas
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor, informe um endereço de e-mail válido.']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A senha deve conter no mínimo 6 caracteres.']);
    exit;
}

if (!empty($passwordConfirm) && $password !== $passwordConfirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'As senhas informadas não coincidem.']);
    exit;
}

$pdo = getDBConnection();

// Verifica se já existe o e-mail
$stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmtCheck->execute([':email' => $email]);
if ($stmtCheck->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Este e-mail já está cadastrado no Cine Palácio.']);
    exit;
}

// Criptografa senha com Bcrypt (Regra CORE)
$hash = hashUserPassword($password);
$nome = explode('@', $email)[0];

$stmtInsert = $pdo->prepare("
    INSERT INTO users (email, password, nome)
    VALUES (:email, :password, :nome)
");

$stmtInsert->execute([
    ':email'    => $email,
    ':password' => $hash,
    ':nome'     => ucfirst($nome)
]);

$newUserId = (int)$pdo->lastInsertId();

// Registra auditoria
logAudit($pdo, $newUserId, $email, 'CADASTRO', 'Novo usuário cadastrado com sucesso via e-mail e senha.');

// Faz o login automático
loginUser([
    'id'    => $newUserId,
    'email' => $email,
    'nome'  => ucfirst($nome)
]);

echo json_encode([
    'success'  => true,
    'message'  => 'Cadastro realizado com sucesso! Bem-vindo ao Cine Palácio.',
    'redirect' => 'index.php'
]);
