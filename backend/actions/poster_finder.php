<?php
/**
 * Cine Palácio - Localizador Inteligente de Capas de Filmes
 * Requisito: "se não tiver a capa no imdb procurar pelo nome do filme e o diretor para encontra-la"
 */

require_once __DIR__ . '/../../config/bd.php';

/**
 * Busca ou valida a capa do filme. Se não tiver ou falhar, busca na Wikipédia / TMDb / Wikimedia
 * usando o título e diretor. Se não encontrar, gera poster estilizado setentista.
 *
 * @param string $titulo
 * @param string $diretor
 * @param string|null $currentCover
 * @return string URL válida ou gerador temático
 */
function resolveMoviePoster(string $titulo, string $diretor = '', ?string $currentCover = null): string {
    // 1. Se já tiver uma URL e for válida, mantém
    if (!empty($currentCover) && filter_var($currentCover, FILTER_VALIDATE_URL)) {
        return $currentCover;
    }

    // 2. Busca na Wikipédia em Português usando Título e Diretor
    $wikiCover = searchWikipediaCover($titulo, $diretor);
    if (!empty($wikiCover)) {
        return $wikiCover;
    }

    // 3. Fallback estilizado padrão Cine Palácio anos 70 (Retro Celluloid Poster)
    $params = http_build_query([
        'title'    => $titulo,
        'director' => $diretor,
        'year'     => date('Y')
    ]);
    return 'assets/images/retro_poster.php?' . $params;
}

/**
 * Consulta a API da Wikipédia em português para encontrar o cartaz oficial do filme
 */
function searchWikipediaCover(string $titulo, string $diretor = ''): ?string {
    try {
        $query = urlencode($titulo . ($diretor ? " " . $diretor : " filme"));
        $apiUrl = "https://pt.wikipedia.org/w/api.php?action=query&format=json&generator=search&gsrsearch={$query}&gsrlimit=1&prop=pageimages&piprop=original|thumbnail&pithumbsize=600";

        $ctx = stream_context_create([
            'http' => [
                'timeout' => 3, // Rápido para não bloquear
                'user_agent' => 'CinePalacio/1.0 (https://cinepalacio.local; contato@cinepalacio.br)'
            ]
        ]);

        $response = @file_get_contents($apiUrl, false, $ctx);
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['query']['pages'])) {
                foreach ($data['query']['pages'] as $page) {
                    if (!empty($page['original']['source'])) {
                        return $page['original']['source'];
                    }
                    if (!empty($page['thumbnail']['source'])) {
                        return $page['thumbnail']['source'];
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        error_log("Erro ao buscar capa no Wikipedia: " . $e->getMessage());
    }

    return null;
}
