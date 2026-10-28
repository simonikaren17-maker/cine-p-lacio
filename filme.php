<?php
/**
 * Cine Palácio - Detalhes do Filme
 * Exibe Capa, Sinopse, Classificação Indicativa Oficial, Diretor, Elenco,
 * Avaliação Interativa por Estrelas e Ações de Assistido/Gostei.
 */

require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/backend/actions/poster_finder.php';

$filmeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($filmeId <= 0) {
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();
$userId = getLoggedUserId();

// Busca o filme com status do usuário
$stmt = $pdo->prepare("
    SELECT 
        f.*,
        uf.assistido AS user_assistido,
        uf.gostei AS user_gostei,
        fa.estrelas AS user_estrelas,
        (SELECT AVG(estrelas) FROM filme_avaliacoes WHERE filme_id = f.id) AS media_estrelas,
        (SELECT COUNT(*) FROM filme_avaliacoes WHERE filme_id = f.id) AS total_avaliacoes
    FROM filmes f
    LEFT JOIN user_filmes uf ON uf.filme_id = f.id AND uf.user_id = :uid1
    LEFT JOIN filme_avaliacoes fa ON fa.filme_id = f.id AND fa.user_id = :uid2
    WHERE f.id = :id
    LIMIT 1
");

$stmt->execute([
    ':uid1' => $userId ?: 0,
    ':uid2' => $userId ?: 0,
    ':id'   => $filmeId
]);

$filme = $stmt->fetch();

if (!$filme) {
    header('Location: index.php?msg=not_found');
    exit;
}

$pageTitle = $filme['titulo'] . ' (' . $filme['ano'] . ')';
$capaUrl = resolveMoviePoster($filme['titulo'], $filme['diretor'], $filme['capa_url']);
$classificacao = $filme['classificacao_indicativa'] ?: '14';
$isWatched = !empty($filme['user_assistido']) && (int)$filme['user_assistido'] === 1;
$isFavorite = !empty($filme['user_gostei']) && (int)$filme['user_gostei'] === 1;
$userRating = (int)($filme['user_estrelas'] ?? 0);
$mediaEstrelas = $filme['media_estrelas'] ? round((float)$filme['media_estrelas'], 1) : round((float)$filme['imdb_rating'] / 2, 1);
$totalAvaliacoes = (int)($filme['total_avaliacoes'] ?? 0);

// Busca filmes relacionados (mesmo diretor ou gênero similar)
$primeiroGenero = trim(explode(',', $filme['generos'])[0] ?? '');
$stmtRel = $pdo->prepare("
    SELECT id, titulo, ano, diretor, generos, classificacao_indicativa, capa_url, imdb_rating, is_cult
    FROM filmes 
    WHERE id != :id AND (generos LIKE :gen OR diretor = :dir)
    ORDER BY imdb_rating DESC 
    LIMIT 4
");
$stmtRel->execute([
    ':id'  => $filmeId,
    ':gen' => '%' . $primeiroGenero . '%',
    ':dir' => $filme['diretor']
]);
$relacionados = $stmtRel->fetchAll();

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 pb-16">
    
    <!-- Topo / Banner Cinemático com Vinheta Retrô -->
    <div class="relative bg-gradient-to-b from-[#18120c] via-[#140e09] to-[#120d09] border-b border-[#3d291a] py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb Retrô -->
            <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6 font-mono">
                <a href="index.php" class="hover:text-[#e5a93c]">Cine Palácio</a>
                <span>/</span>
                <a href="index.php?genero=<?= urlencode($primeiroGenero) ?>" class="hover:text-[#e5a93c]"><?= htmlspecialchars($primeiroGenero) ?></a>
                <span>/</span>
                <span class="text-[#fde68a] truncate"><?= htmlspecialchars($filme['titulo']) ?></span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Coluna Esquerda: Cartaz com Selo Oficial de Classificação Indicativa -->
                <div class="md:col-span-4 lg:col-span-4">
                    <div class="relative bg-[#1c140e] border-2 border-[#b45309] rounded-2xl p-2 shadow-2xl glow-amber">
                        
                        <div class="relative aspect-[2/3] w-full rounded-xl overflow-hidden bg-[#140e09]">
                            <img 
                                src="<?= htmlspecialchars($capaUrl) ?>" 
                                alt="Cartaz Oficial de <?= htmlspecialchars($filme['titulo']) ?>"
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.src='assets/images/retro_poster.php?title=<?= urlencode($filme['titulo']) ?>&director=<?= urlencode($filme['diretor']) ?>&year=<?= $filme['ano'] ?>';"
                            />

                            <!-- Selo Oficial de Classificação Indicativa (Grande no Detalhe) -->
                            <div class="absolute top-3 left-3 shadow-2xl" title="Classificação Indicativa Oficial do Brasil: <?= htmlspecialchars($classificacao) ?>">
                                <span class="badge-classificacao large cl-<?= htmlspecialchars($classificacao) ?>">
                                    <?= htmlspecialchars($classificacao) ?>
                                </span>
                            </div>

                            <!-- Tag Notoriedade -->
                            <div class="absolute top-3 right-3">
                                <?php if (!empty($filme['is_cult'])): ?>
                                    <span class="px-2.5 py-1 rounded text-[11px] font-mono tracking-wider font-bold bg-[#8a2524] text-[#fde68a] border border-[#e5a93c]/40 shadow-lg">
                                        LADO B • CULT
                                    </span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded text-[11px] font-mono tracking-wider font-bold bg-[#b45309] text-white border border-[#fde68a]/40 shadow-lg">
                                        CLÁSSICO NACIONAL
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Ações Rápidas Abaixo do Cartaz -->
                        <div class="grid grid-cols-2 gap-2 mt-3 pt-1">
                            <button 
                                type="button" 
                                class="btn-toggle-favorite flex items-center justify-center gap-2 py-2.5 rounded-lg border border-[#3d291a] text-xs font-bold transition-all <?= $isFavorite ? 'bg-[#8a2524] text-white' : 'bg-[#140e09] text-stone-300 hover:text-white' ?>"
                                data-filme-id="<?= $filme['id'] ?>"
                            >
                                <svg class="w-4 h-4 <?= $isFavorite ? 'text-rose-400' : 'text-stone-400' ?>" fill="<?= $isFavorite ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span><?= $isFavorite ? 'Gostei!' : 'Gostei' ?></span>
                            </button>

                            <button 
                                type="button" 
                                class="btn-toggle-watched flex items-center justify-center gap-2 py-2.5 rounded-lg border border-[#3d291a] text-xs font-bold transition-all <?= $isWatched ? 'bg-[#b45309] text-white' : 'bg-[#140e09] text-stone-300 hover:text-white' ?>"
                                data-filme-id="<?= $filme['id'] ?>"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span><?= $isWatched ? 'Já Assisti' : 'Assistir' ?></span>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Coluna Direita: Informações Detalhadas, Sinopse e Avaliação por Estrelas -->
                <div class="md:col-span-8 lg:col-span-8 space-y-6">
                    
                    <!-- Cabeçalho do Filme -->
                    <div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-[#e5a93c] font-mono mb-2">
                            <span class="font-bold text-base"><?= htmlspecialchars($filme['ano']) ?></span>
                            <span>•</span>
                            <span><?= (int)$filme['duracao_minutos'] ?> MIN</span>
                            <span>•</span>
                            <span class="text-stone-300"><?= htmlspecialchars($filme['generos']) ?></span>
                            <?php if ($filme['imdb_id']): ?>
                                <span>•</span>
                                <a href="https://www.imdb.com/pt/title/<?= htmlspecialchars($filme['imdb_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-1 bg-[#f5c518] text-black font-bold px-1.5 py-0.5 rounded text-[10px] hover:opacity-90">
                                    IMDb: ★ <?= number_format($filme['imdb_rating'], 1) ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <h1 class="font-serif-vintage text-3xl sm:text-4xl lg:text-5xl font-black text-[#fde68a] leading-tight">
                            <?= htmlspecialchars($filme['titulo']) ?>
                        </h1>

                        <?php if ($filme['titulo_original'] && $filme['titulo_original'] !== $filme['titulo']): ?>
                            <p class="text-xs text-stone-400 mt-1 italic">
                                Título Original: <?= htmlspecialchars($filme['titulo_original']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Bloco de Classificação por Estrelas (Interativa) -->
                    <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-5 shadow-lg space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] font-bold">
                                    Sua Avaliação (Clique para Classificar)
                                </span>
                                <div class="star-rating star-rating-interactive mt-1" data-filme-id="<?= $filme['id'] ?>" data-user-rating="<?= $userRating ?>">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <button 
                                            type="button" 
                                            class="star-btn <?= ($userRating >= $s) ? 'text-[#e5a93c]' : 'text-stone-600' ?>" 
                                            data-star="<?= $s ?>"
                                            title="Dar <?= $s ?> estrela(s)"
                                        >
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="text-left sm:text-right border-t sm:border-t-0 sm:border-l border-[#3d291a] pt-2 sm:pt-0 sm:pl-6">
                                <span class="block text-xs text-stone-400">Média Cine Palácio</span>
                                <span class="font-poster text-2xl text-[#fde68a] filme-rating-avg-<?= $filme['id'] ?>">
                                    ★ <?= $mediaEstrelas ?> <span class="text-xs font-sans text-stone-400 font-normal">(<?= $totalAvaliacoes ?> votos)</span>
                                </span>
                            </div>
                        </div>

                        <?php if (!$userId): ?>
                            <p class="text-xs text-stone-400">
                                <a href="login.php" class="text-amber-400 hover:underline">Entre</a> ou <a href="cadastro.php" class="text-amber-400 hover:underline">cadastre-se</a> para salvar sua classificação no seu histórico pessoal.
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Sinopse Completa (Requisito CORE) -->
                    <div class="space-y-2">
                        <h3 class="font-poster text-lg tracking-wider text-[#fde68a] uppercase">
                            Sinopse da Obra
                        </h3>
                        <p class="text-sm sm:text-base text-stone-200 leading-relaxed font-sans bg-[#140e09]/60 p-4 rounded-xl border border-[#3d291a]/60">
                            <?= nl2br(htmlspecialchars($filme['sinopse'])) ?>
                        </p>
                    </div>

                    <!-- Ficha Técnica: Direção e Elenco -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="bg-[#1c140e] p-4 rounded-xl border border-[#3d291a]">
                            <span class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">Direção</span>
                            <span class="text-sm text-stone-200 font-medium"><?= htmlspecialchars($filme['diretor'] ?: 'Direção não informada') ?></span>
                        </div>

                        <div class="bg-[#1c140e] p-4 rounded-xl border border-[#3d291a]">
                            <span class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">Classificação Indicativa</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="badge-classificacao cl-<?= htmlspecialchars($classificacao) ?>">
                                    <?= htmlspecialchars($classificacao) ?>
                                </span>
                                <span class="text-xs text-stone-300">
                                    <?php
                                    switch (strtoupper($classificacao)) {
                                        case 'L':
                                        case 'LIVRE': echo 'Livre para todos os públicos'; break;
                                        case '10': echo 'Não recomendado para menores de 10 anos'; break;
                                        case '12': echo 'Não recomendado para menores de 12 anos'; break;
                                        case '14': echo 'Não recomendado para menores de 14 anos'; break;
                                        case '16': echo 'Não recomendado para menores de 16 anos'; break;
                                        case '18': echo 'Não recomendado para menores de 18 anos'; break;
                                        default: echo 'Classificação indicativa nacional'; break;
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="sm:col-span-2 bg-[#1c140e] p-4 rounded-xl border border-[#3d291a]">
                            <span class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">Elenco Principal</span>
                            <span class="text-sm text-stone-300"><?= htmlspecialchars($filme['elenco'] ?: 'Elenco não catalogado') ?></span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Seção de Filmes Relacionados -->
    <?php if (!empty($relacionados)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <h3 class="font-poster text-xl text-[#fde68a] tracking-wide mb-6">
            Obras Semelhantes em Cartaz
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($relacionados as $filme): ?>
                <?php include __DIR__ . '/views/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
