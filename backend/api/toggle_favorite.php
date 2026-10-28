<?php
/**
 * Cine Palácio - API para favoritar / desfavoritar filme ("Gostei")
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
        'message' => 'Cadastre-se ou entre para guardar os filmes que você gostou!'
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

$stmtF = $pdo->prepare("SELECT id, titulo FROM filmes WHERE id = :id LIMIT 1");
$stmtF->execute([':id' => $filmeId]);
$filme = $stmtF->fetch();

if (!$filme) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Filme não encontrado.']);
    exit;
}

// Verifica status
$stmtStatus = $pdo->prepare("SELECT gostei FROM user_filmes WHERE user_id = :uid AND filme_id = :fid LIMIT 1");
$stmtStatus->execute([':uid' => $userId, ':fid' => $filmeId]);
$row = $stmtStatus->fetch();

$novoGostei = 1;
if ($row && (int)$row['gostei'] === 1) {
    $novoGostei = 0;
}

$gosteiEm = ($novoGostei === 1) ? date('Y-m-d H:i:s') : null;

$stmtSave = $pdo->prepare("
    INSERT INTO user_filmes (user_id, filme_id, gostei, gostei_em)
    VALUES (:uid, :fid, :gostei, :gostei_em)
    ON DUPLICATE KEY UPDATE 
        gostei = :gostei_up, 
        gostei_em = :gostei_em_up,
        updated_at = NOW()
");

$stmtSave->execute([
    ':uid'          => $userId,
    ':fid'          => $filmeId,
    ':gostei'       => $novoGostei,
    ':gostei_em'    => $gosteiEm,
    ':gostei_up'    => $novoGostei,
    ':gostei_em_up' => $gosteiEm
]);

$acao = $novoGostei ? 'FAVORITAR' : 'DESFAVORITAR';
$msg = $novoGostei 
    ? sprintf("Você adicionou '%s' aos seus filmes favoritos!", $filme['titulo'])
    : sprintf("'%s' foi removido dos seus favoritos.", $filme['titulo']);

// Auditoria
logAudit($pdo, $userId, $userEmail, $acao, sprintf("%s o filme '%s' (ID: %d).", $novoGostei ? 'Favoritou / Marcou que gostou de' : 'Desfavoritou', $filme['titulo'], $filmeId));

echo json_encode([
    'success'  => true,
    'gostei'   => (bool)$novoGostei,
    'filme_id' => $filmeId,
    'message'  => $msg
]);
