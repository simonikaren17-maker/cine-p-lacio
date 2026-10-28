<?php
/**
 * Cine Palácio - API de Busca Dinâmica (Live Search)
 * Retorna sugestões instantâneas ao digitar na barra de pesquisa.
 */

require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../actions/poster_finder.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');

if (mb_strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT id, titulo, ano, diretor, generos, classificacao_indicativa, imdb_rating, capa_url, is_cult
    FROM filmes
    WHERE titulo LIKE :q1 
       OR diretor LIKE :q2 
       OR elenco LIKE :q3 
       OR generos LIKE :q4
    ORDER BY 
        CASE 
            WHEN titulo LIKE :exact THEN 1
            WHEN titulo LIKE :starts THEN 2
            ELSE 3
        END,
        imdb_rating DESC
    LIMIT 8
");

$searchTerm = '%' . $query . '%';
$searchStarts = $query . '%';

$stmt->execute([
    ':q1'     => $searchTerm,
    ':q2'     => $searchTerm,
    ':q3'     => $searchTerm,
    ':q4'     => $searchTerm,
    ':exact'  => $query,
    ':starts' => $searchStarts
]);

$filmes = $stmt->fetchAll();

$results = array_map(function($f) {
    return [
        'id'            => (int)$f['id'],
        'titulo'        => $f['titulo'],
        'ano'           => (int)$f['ano'],
        'diretor'       => $f['diretor'],
        'generos'       => $f['generos'],
        'classificacao' => $f['classificacao_indicativa'],
        'rating'        => (float)$f['imdb_rating'],
        'is_cult'       => (bool)$f['is_cult'],
        'capa_url'      => resolveMoviePoster($f['titulo'], $f['diretor'], $f['capa_url']),
        'url'           => 'filme.php?id=' . $f['id']
    ];
}, $filmes);

echo json_encode([
    'success' => true,
    'total'   => count($results),
    'results' => $results
]);
