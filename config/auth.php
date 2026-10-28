<?php
/**
 * Cine Palácio - Módulo de Autenticação e Sessões Nativas
 * Trata senhas com hash seguro Bcrypt.
 */

require_once __DIR__ . '/bd.php';
require_once __DIR__ . '/audit.php';

// Inicia a sessão nativa com configurações de segurança
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Parâmetros de cookie seguros
        $lifetime = 60 * 60 * 24 * 7; // 7 dias
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

// Inicia automaticamente ao incluir
initSession();

/**
 * Verifica se há um usuário autenticado na sessão
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Retorna os dados do usuário atualmente logado ou null
 */
function getLoggedUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id'    => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? '',
        'nome'  => $_SESSION['user_nome'] ?? explode('@', $_SESSION['user_email'] ?? '')[0]
    ];
}

/**
 * Retorna o ID do usuário autenticado ou null
 */
function getLoggedUserId(): ?int {
    return isLoggedIn() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Cria o hash de senha utilizando Bcrypt (conforme regra de negócio CORE)
 */
function hashUserPassword(string $plainPassword): string {
    return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Valida a senha informada contra o hash Bcrypt
 */
function verifyUserPassword(string $plainPassword, string $hash): bool {
    return password_verify($plainPassword, $hash);
}

/**
 * Realiza o login na sessão e registra log de auditoria
 */
function loginUser(array $user): void {
    // Regenera ID da sessão para evitar Session Fixation
    session_regenerate_id(true);

    $_SESSION['user_id']    = (int)$user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_nome']  = !empty($user['nome']) ? $user['nome'] : explode('@', $user['email'])[0];

    // Registra auditoria
    $pdo = getDBConnection();
    logAudit($pdo, (int)$user['id'], $user['email'], 'LOGIN', 'Usuário efetuou login no Cine Palácio com sucesso.');
}

/**
 * Encerra a sessão do usuário e registra log
 */
function logoutUser(): void {
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'] ?? 'desconhecido';
        $pdo = getDBConnection();
        logAudit($pdo, (int)$userId, $userEmail, 'LOGOUT', 'Usuário efetuou logout do Cine Palácio.');
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Exige login para acessar a rota; se não estiver, redireciona ou retorna erro JSON
 */
function requireLogin(bool $isJson = false): void {
    if (!isLoggedIn()) {
        if ($isJson) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Você precisa estar logado para realizar esta ação no Cine Palácio.',
                'require_login' => true
            ]);
            exit;
        }

        header('Location: login.php?msg=auth_required');
        exit;
    }
}
