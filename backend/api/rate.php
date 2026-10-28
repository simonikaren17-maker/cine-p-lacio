<?php
/**
 * Cine Palácio - API de Classificação por Estrelas (1 a 5)
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
        'message' => 'Você precisa entrar na sua conta para avaliar com estrelas.'
    ]);
    exit;
}

$userId = getLoggedUserId();
$userEmail = $_SESSION['user_email'] ?? '';
$filmeId = isset($_POST['filme_id']) ? (int)$_POST['filme_id'] : 0;
$estrelas = isset($_POST['estrelas']) ? (int)$_POST['estrelas'] : 0;

if ($filmeId <= 0 || $estrelas < 1 || $estrelas > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Classificação inválida. Escolha entre 1 e 5 estrelas.']);
    exit;
}

$pdo = getDBConnection();

// Busca o filme para validação e título
$stmtF = $pdo->prepare("SELECT id, titulo FROM filmes WHERE id = :id LIMIT 1");
$stmtF->execute([':id' => $filmeId]);
$filme = $stmtF->fetch();

if (!$filme) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Filme não encontrado.']);
    exit;
}

// Salva ou atualiza a avaliação (UPSERT)
$stmtRate = $pdo->prepare("
    INSERT INTO filme_avaliacoes (user_id, filme_id, estrelas, updated_at)
    VALUES (:user_id, :filme_id, :estrelas, NOW())
    ON DUPLICATE KEY UPDATE estrelas = :estrelas_update, updated_at = NOW()
");

$stmtRate->execute([
    ':user_id' => $userId,
    ':filme_id' => $filmeId,
    ':estrelas' => $estrelas,
    ':estrelas_update' => $estrelas
]);

// Calcula nova média de estrelas da comunidade para este filme
$stmtAvg = $pdo->prepare("
    SELECT AVG(estrelas) as media_estrelas, COUNT(*) as total_votos 
    FROM filme_avaliacoes 
    WHERE filme_id = :filme_id
");
$stmtAvg->execute([':filme_id' => $filmeId]);
$stats = $stmtAvg->fetch();
$mediaEstrelas = round((float)$stats['media_estrelas'], 1);
$totalVotos = (int)$stats['total_votos'];

// Registra log de auditoria obrigatório
logAudit(
    $pdo,
    $userId,
    $userEmail,
    'AVALIAR_FILME',
    sprintf("Classificou o filme '%s' (ID: %d) com %d estrela(s).", $filme['titulo'], $filmeId, $estrelas)
);

echo json_encode([
    'success'        => true,
    'message'        => sprintf("Você avaliou '%s' com %d estrela(s)!", $filme['titulo'], $estrelas),
    'filme_id'       => $filmeId,
    'user_estrelas'  => $estrelas,
    'media_estrelas' => $mediaEstrelas,
    'total_votos'    => $totalVotos
]);
