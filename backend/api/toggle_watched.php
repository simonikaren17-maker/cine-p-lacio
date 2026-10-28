<?php
/**
 * Cine Palácio - API para marcar/desmarcar filme como assistido
 */

require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/audit.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'require_login' => true,
        'message' => 'Cadastre-se ou entre para marcar os filmes que você já assistiu!'
    ]);
    exit;
}

$userId = getLoggedUserId();
$userEmail = $_SESSION['user_email'] ?? '';
$filmeId = isset($_POST['filme_id']) ? (int)$_POST['filme_id'] : 0;

if ($filmeId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de filme inválido.']);
    exit;
}

$pdo = getDBConnection();

// Valida filme
$stmtF = $pdo->prepare("SELECT id, titulo FROM filmes WHERE id = :id LIMIT 1");
$stmtF->execute([':id' => $filmeId]);
$filme = $stmtF->fetch();

if (!$filme) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Filme não encontrado.']);
    exit;
}

// Verifica status atual
$stmtStatus = $pdo->prepare("SELECT assistido, gostei FROM user_filmes WHERE user_id = :uid AND filme_id = :fid LIMIT 1");
$stmtStatus->execute([':uid' => $userId, ':fid' => $filmeId]);
$row = $stmtStatus->fetch();

$novoAssistido = 1;
if ($row && (int)$row['assistido'] === 1) {
    $novoAssistido = 0;
}

$assistidoEm = ($novoAssistido === 1) ? date('Y-m-d H:i:s') : null;

$stmtSave = $pdo->prepare("
    INSERT INTO user_filmes (user_id, filme_id, assistido, assistido_em)
    VALUES (:uid, :fid, :assistido, :assistido_em)
    ON DUPLICATE KEY UPDATE 
        assistido = :assistido_up, 
        assistido_em = :assistido_em_up,
        updated_at = NOW()
");

$stmtSave->execute([
    ':uid'             => $userId,
    ':fid'             => $filmeId,
    ':assistido'       => $novoAssistido,
    ':assistido_em'    => $assistidoEm,
    ':assistido_up'    => $novoAssistido,
    ':assistido_em_up' => $assistidoEm
]);

$acao = $novoAssistido ? 'MARCAR_ASSISTIDO' : 'DESMARCAR_ASSISTIDO';
$msg = $novoAssistido 
    ? sprintf("'%s' foi marcado como assistido! Ele não aparecerá mais nas sugestões.", $filme['titulo'])
    : sprintf("'%s' foi desmarcado da sua lista de assistidos.", $filme['titulo']);

// Auditoria
logAudit($pdo, $userId, $userEmail, $acao, sprintf("%s o filme '%s' (ID: %d).", $novoAssistido ? 'Marcou como assistido' : 'Removeu dos assistidos', $filme['titulo'], $filmeId));

echo json_encode([
    'success'   => true,
    'assistido' => (bool)$novoAssistido,
    'filme_id'  => $filmeId,
    'message'   => $msg
]);
