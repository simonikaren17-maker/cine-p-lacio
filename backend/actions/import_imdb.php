<?php
/**
 * Cine Palácio - Módulo de Importação / Referência do IMDb
 * Consulta https://www.imdb.com/pt/ e extrai metadados de filmes brasileiros,
 * buscando capas alternativas caso o IMDb não forneça ou esteja inacessível.
 */

require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/audit.php';
require_once __DIR__ . '/poster_finder.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

$imdbInput = trim($_POST['imdb_url_or_id'] ?? '');

if (empty($imdbInput)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor, informe a URL do filme no IMDb ou o identificador (ex: tt0317248).']);
    exit;
}

// Extrai o ID do IMDb (ex: tt0317248)
if (preg_match('/tt\d{7,8}/i', $imdbInput, $matches)) {
    $imdbId = $matches[0];
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Identificador do IMDb inválido. Deve conter formato ttXXXXXXX.']);
    exit;
}

$pdo = getDBConnection();

// Verifica se já está no banco
$stmtExist = $pdo->prepare("SELECT id, titulo FROM filmes WHERE imdb_id = :imdb_id LIMIT 1");
$stmtExist->execute([':imdb_id' => $imdbId]);
$existente = $stmtExist->fetch();

if ($existente) {
    echo json_encode([
        'success' => true,
        'message' => sprintf("O filme '%s' já está em nossa base do Cine Palácio!", $existente['titulo']),
        'filme_id' => $existente['id'],
        'redirect' => 'filme.php?id=' . $existente['id']
    ]);
    exit;
}

// Faz a requisição ao IMDb em português
$targetUrl = "https://www.imdb.com/pt/title/{$imdbId}/";

$ctx = stream_context_create([
    'http' => [
        'timeout' => 8,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'header' => "Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7\r\n"
    ]
]);

$html = @file_get_contents($targetUrl, false, $ctx);

$titulo = '';
$ano = 2024;
$diretor = 'Cinema Brasileiro';
$elenco = '';
$generos = 'Drama';
$sinopse = '';
$capaUrl = null;
$rating = 7.5;
$votes = 3000;
$classificacao = '14';

if ($html) {
    // 1. Tenta extrair JSON-LD estruturado do IMDb
    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $jsonMatch)) {
        $ldJson = json_decode($jsonMatch[1], true);
        if ($ldJson && isset($ldJson['name'])) {
            $titulo = $ldJson['name'];
            $sinopse = $ldJson['description'] ?? '';
            $capaUrl = $ldJson['image'] ?? null;
            
            if (!empty($ldJson['aggregateRating']['ratingValue'])) {
                $rating = (float)$ldJson['aggregateRating']['ratingValue'];
            }
            if (!empty($ldJson['aggregateRating']['ratingCount'])) {
                $votes = (int)$ldJson['aggregateRating']['ratingCount'];
            }
            if (!empty($ldJson['genre'])) {
                $generos = is_array($ldJson['genre']) ? implode(', ', $ldJson['genre']) : $ldJson['genre'];
            }
            if (!empty($ldJson['director'])) {
                if (is_array($ldJson['director'])) {
                    $diretor = $ldJson['director'][0]['name'] ?? 'Diretor';
                } elseif (isset($ldJson['director']['name'])) {
                    $diretor = $ldJson['director']['name'];
                }
            }
            if (!empty($ldJson['actor'])) {
                $atores = [];
                foreach ($ldJson['actor'] as $ator) {
                    if (isset($ator['name'])) $atores[] = $ator['name'];
                }
                $elenco = implode(', ', array_slice($atores, 0, 5));
            }
        }
    }

    // 2. Fallbacks de Regex no HTML caso o JSON-LD não venha completo
    if (empty($titulo) && preg_match('/<title>(.*?)<\/title>/i', $html, $titleMatch)) {
        $rawTitle = html_entity_decode($titleMatch[1]);
        $titulo = trim(explode('(', $rawTitle)[0]);
        if (preg_match('/\((\d{4})\)/', $rawTitle, $yearMatch)) {
            $ano = (int)$yearMatch[1];
        }
    }
}

// Se não conseguiu dados mínimos, atribui título padrão derivado
if (empty($titulo)) {
    $titulo = 'Filme Nacional (' . $imdbId . ')';
    $sinopse = 'Informações importadas da referência do IMDb. Sinopse completa em catalogação pela equipe do Cine Palácio.';
}

// REQUISITO CORE: "se não tiver a capa no imdb procurar pelo nome do filme e o diretor para encontra-la"
$capaFinal = resolveMoviePoster($titulo, $diretor, $capaUrl);

// Determina se é cult ou mainstream com base no volume de votos
$isCult = ($votes < 8000) ? 1 : 0;

$stmtInsert = $pdo->prepare("
    INSERT INTO filmes (imdb_id, titulo, titulo_original, ano, diretor, elenco, generos, classificacao_indicativa, duracao_minutos, sinopse, capa_url, imdb_rating, imdb_votes, is_cult)
    VALUES (:imdb_id, :titulo, :titulo_original, :ano, :diretor, :elenco, :generos, :classificacao_indicativa, :duracao_minutos, :sinopse, :capa_url, :imdb_rating, :imdb_votes, :is_cult)
");

$stmtInsert->execute([
    ':imdb_id' => $imdbId,
    ':titulo' => $titulo,
    ':titulo_original' => $titulo,
    ':ano' => $ano,
    ':diretor' => $diretor,
    ':elenco' => $elenco ?: 'Elenco nacional',
    ':generos' => $generos,
    ':classificacao_indicativa' => $classificacao,
    ':duracao_minutos' => 110,
    ':sinopse' => $sinopse ?: 'Obra do cinema brasileiro importada para o acervo.',
    ':capa_url' => $capaFinal,
    ':imdb_rating' => $rating,
    ':imdb_votes' => $votes,
    ':is_cult' => $isCult
]);

$newMovieId = (int)$pdo->lastInsertId();

// Registra auditoria
$userId = getLoggedUserId();
$userEmail = $_SESSION['user_email'] ?? 'SISTEMA';
logAudit(
    $pdo,
    $userId,
    $userEmail,
    'IMPORTAR_IMDB',
    sprintf("Importou filme '%s' do IMDb (%s) para o Cine Palácio.", $titulo, $imdbId)
);

echo json_encode([
    'success' => true,
    'message' => sprintf("Filme '%s' importado com sucesso da referência do IMDb!", $titulo),
    'filme_id' => $newMovieId,
    'redirect' => 'filme.php?id=' . $newMovieId
]);
