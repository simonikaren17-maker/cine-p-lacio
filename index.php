<?php
/**
 * Cine Palácio - Catálogo Principal & Descoberta
 * Filtros por Mais/Menos conhecidos, Gêneros e Ocultação de Assistidos.
 */

$pageTitle = 'Catálogo de Filmes Brasileiros';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/backend/actions/poster_finder.php';

$pdo = getDBConnection();
$userId = getLoggedUserId();

// Captura de Filtros da URL
$filtroTipo = $_GET['filtro'] ?? ''; // 'mainstream', 'cult', ou vazio (todos)
$filtroGenero = trim($_GET['genero'] ?? '');
$ordem = $_GET['ordem'] ?? 'rating_desc';
$ocultarAssistidos = isset($_GET['ocultar_assistidos']) ? (int)$_GET['ocultar_assistidos'] : ($userId ? 1 : 0);

// Lista dinâmica de gêneros disponíveis
$stmtGeneros = $pdo->query("SELECT DISTINCT generos FROM filmes");
$generosRaw = $stmtGeneros->fetchAll(PDO::FETCH_COLUMN);
$listaGeneros = [];
foreach ($generosRaw as $gString) {
    $parts = explode(',', $gString);
    foreach ($parts as $p) {
        $pClean = trim($p);
        if (!empty($pClean) && !in_array($pClean, $listaGeneros)) {
            $listaGeneros[] = $pClean;
        }
    }
}
sort($listaGeneros);

// Construção da Query Segura via PDO
$where = [];
$params = [];

// Filtro: Mais Conhecidos vs Menos Conhecidos (Cult)
if ($filtroTipo === 'mainstream') {
    $where[] = "f.is_cult = 0";
} elseif ($filtroTipo === 'cult') {
    $where[] = "f.is_cult = 1";
}

// Filtro: Gênero
if (!empty($filtroGenero)) {
    $where[] = "f.generos LIKE :genero";
    $params[':genero'] = '%' . $filtroGenero . '%';
}

// REGRA CORE: "filmes marcados como assistido não deve aparecer na lista de novas sugestões"
if ($userId && $ocultarAssistidos === 1) {
    $where[] = "(uf.assistido IS NULL OR uf.assistido = 0)";
}

// Ordenação
$orderSql = "f.imdb_rating DESC, f.ano DESC";
switch ($ordem) {
    case 'ano_desc':
        $orderSql = "f.ano DESC";
        break;
    case 'ano_asc':
        $orderSql = "f.ano ASC";
        break;
    case 'titulo_asc':
        $orderSql = "f.titulo ASC";
        break;
    case 'rating_asc':
        $orderSql = "f.imdb_rating ASC";
        break;
    default:
        $orderSql = "f.imdb_rating DESC, f.ano DESC";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Consulta com JOIN para trazer status do usuário logado e médias
$sql = "
    SELECT 
        f.*,
        uf.assistido AS user_assistido,
        uf.gostei AS user_gostei,
        fa.estrelas AS user_estrelas,
        (SELECT AVG(estrelas) FROM filme_avaliacoes WHERE filme_id = f.id) AS media_estrelas,
        (SELECT COUNT(*) FROM filme_avaliacoes WHERE filme_id = f.id) AS total_avaliacoes
    FROM filmes f
    LEFT JOIN user_filmes uf ON uf.filme_id = f.id AND uf.user_id = :user_id_join
    LEFT JOIN filme_avaliacoes fa ON fa.filme_id = f.id AND fa.user_id = :user_id_join2
    {$whereClause}
    ORDER BY {$orderSql}
";

$stmt = $pdo->prepare($sql);
$executeParams = array_merge($params, [
    ':user_id_join'  => $userId ?: 0,
    ':user_id_join2' => $userId ?: 0
]);
$stmt->execute($executeParams);
$filmes = $stmt->fetchAll();

// Filme em destaque especial no topo (O Agente Secreto ou Cidade de Deus)
$stmtHero = $pdo->prepare("SELECT * FROM filmes WHERE titulo LIKE '%O Agente Secreto%' OR titulo LIKE '%Cidade de Deus%' ORDER BY ano DESC LIMIT 1");
$stmtHero->execute();
$heroFilme = $stmtHero->fetch();

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 pb-16">
    
    <!-- ========================================================================
         HERO SECTION: ESTÉTICA ANOS 70 / O AGENTE SECRETO & CIDADE DE DEUS
         ======================================================================== -->
    <section class="relative bg-gradient-to-b from-[#18120c] via-[#140e09] to-[#120d09] border-b border-[#3d291a] overflow-hidden py-12 md:py-16">
        
        <!-- Fundo de Grão & Vinheta Retrô -->
        <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#e5a93c_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-[#8a2524]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 rounded-full bg-[#b45309]/20 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Chamada Principal do Cine Palácio -->
                <div class="lg:col-span-7 space-y-5">
                    
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#8a2524]/80 border border-[#e5a93c]/40 text-[#fde68a] text-xs font-mono tracking-widest uppercase shadow-md">
                        <span class="w-2 h-2 rounded-full bg-[#e5a93c] animate-ping"></span>
                        Em Cartaz • Mostra Cinema Brasileiro
                    </div>

                    <h1 class="font-poster text-4xl sm:text-5xl lg:text-6xl text-[#fde68a] leading-[1.05] tracking-wide">
                        O CINEMA QUE PULSA NO CORAÇÃO DO BRASIL.
                    </h1>

                    <p class="text-sm sm:text-base text-stone-300 leading-relaxed font-sans max-w-xl">
                        Descubra obras-primas cultuadas, os maiores sucessos de bilheteria e as pérolas desconhecidas do cinema nacional. Da estética setentista à revolução moderna.
                    </p>

                    <!-- Ações Rápidas do Hero -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="index.php?filtro=mainstream" class="px-5 py-2.5 rounded-lg bg-[#b45309] hover:bg-[#d97706] text-[#faf5ea] font-bold text-xs uppercase tracking-wider shadow-lg transition-transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>🎬 Mais Conhecidos</span>
                        </a>
                        <a href="index.php?filtro=cult" class="px-5 py-2.5 rounded-lg bg-[#1c140e] hover:bg-[#261b13] border border-[#e5a93c]/60 text-[#fde68a] font-bold text-xs uppercase tracking-wider shadow-md transition-transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>📼 Lado B & Cult</span>
                        </a>
                        <?php if (!$userId): ?>
                            <a href="cadastro.php" class="px-4 py-2.5 rounded-lg text-xs font-semibold text-stone-300 hover:text-white transition-colors">
                                Criar Minha Conta Grátis →
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Card de Destaque Especial (Estilo Agente Secreto / 1977) -->
                <?php if ($heroFilme): 
                    $heroCover = resolveMoviePoster($heroFilme['titulo'], $heroFilme['diretor'], $heroFilme['capa_url']);
                ?>
                <div class="lg:col-span-5">
                    <div class="relative bg-[#1c140e] rounded-2xl border-2 border-[#b45309] p-4 sm:p-5 shadow-2xl glow-terracotta transform lg:rotate-1 hover:rotate-0 transition-transform duration-300">
                        
                        <div class="flex gap-4 items-center">
                            <img 
                                src="<?= htmlspecialchars($heroCover) ?>" 
                                alt="<?= htmlspecialchars($heroFilme['titulo']) ?>"
                                class="w-28 sm:w-32 aspect-[2/3] object-cover rounded-lg border border-[#e5a93c]/40 shadow-md flex-shrink-0"
                                onerror="this.src='assets/images/retro_poster.php?title=<?= urlencode($heroFilme['titulo']) ?>';"
                            />
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <span class="text-[10px] font-mono tracking-widest text-[#e5a93c] uppercase font-bold">
                                    ★ Grande Destaque do Acervo
                                </span>
                                <h3 class="font-serif-vintage text-xl sm:text-2xl font-bold text-[#fde68a] leading-tight truncate">
                                    <?= htmlspecialchars($heroFilme['titulo']) ?>
                                </h3>
                                <p class="text-xs text-stone-400">
                                    <?= $heroFilme['ano'] ?> • Dir. <?= htmlspecialchars($heroFilme['diretor']) ?>
                                </p>
                                <p class="text-xs text-stone-300 line-clamp-3 leading-snug">
                                    <?= htmlspecialchars($heroFilme['sinopse']) ?>
                                </p>
                                <div class="pt-2">
                                    <a href="filme.php?id=<?= $heroFilme['id'] ?>" class="inline-flex items-center gap-1 text-xs font-bold text-[#e5a93c] hover:text-[#fde68a]">
                                        Ver Ficha Completa →
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </section>

    <!-- ========================================================================
         BARRA DE CONTROLE: FILTROS POR CONHECIDO/CULT, GÊNEROS E ASSISTIDOS
         ======================================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-20">
        <form method="GET" action="index.php" class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-4 sm:p-6 shadow-xl space-y-4">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Abas Rápidas de Notoriedade (Mais Conhecidos vs Menos Conhecidos) -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                    <a href="index.php?genero=<?= urlencode($filtroGenero) ?>&ocultar_assistidos=<?= $ocultarAssistidos ?>" 
                       class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap <?= empty($filtroTipo) ? 'bg-[#b45309] text-white shadow' : 'bg-[#140e09] text-stone-300 hover:bg-[#261b13]' ?>">
                        Todos (<?= count($filmes) ?>)
                    </a>
                    <a href="index.php?filtro=mainstream&genero=<?= urlencode($filtroGenero) ?>&ocultar_assistidos=<?= $ocultarAssistidos ?>" 
                       class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap <?= ($filtroTipo === 'mainstream') ? 'bg-[#b45309] text-white shadow' : 'bg-[#140e09] text-stone-300 hover:bg-[#261b13]' ?>">
                        🎬 Mais Conhecidos
                    </a>
                    <a href="index.php?filtro=cult&genero=<?= urlencode($filtroGenero) ?>&ocultar_assistidos=<?= $ocultarAssistidos ?>" 
                       class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap <?= ($filtroTipo === 'cult') ? 'bg-[#8a2524] text-[#fde68a] shadow' : 'bg-[#140e09] text-stone-300 hover:bg-[#261b13]' ?>">
                        📼 Lado B / Cult
                    </a>
                </div>

                <!-- Controles: Gênero, Ordenação & Checkbox Assistidos -->
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Preserva o filtro tipo -->
                    <?php if ($filtroTipo): ?>
                        <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtroTipo) ?>">
                    <?php endif; ?>

                    <!-- Select Gênero -->
                    <div class="w-full sm:w-auto">
                        <select name="genero" onchange="this.form.submit()" class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] text-xs text-[#faf5ea] rounded-lg px-3 py-2 focus:outline-none">
                            <option value="">Todos os Gêneros</option>
                            <?php foreach ($listaGeneros as $gen): ?>
                                <option value="<?= htmlspecialchars($gen) ?>" <?= ($filtroGenero === $gen) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($gen) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Select Ordenação -->
                    <div class="w-full sm:w-auto">
                        <select name="ordem" onchange="this.form.submit()" class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] text-xs text-[#faf5ea] rounded-lg px-3 py-2 focus:outline-none">
                            <option value="rating_desc" <?= ($ordem === 'rating_desc') ? 'selected' : '' ?>>Mais bem avaliados</option>
                            <option value="ano_desc" <?= ($ordem === 'ano_desc') ? 'selected' : '' ?>>Mais recentes primeiro</option>
                            <option value="ano_asc" <?= ($ordem === 'ano_asc') ? 'selected' : '' ?>>Clássicos mais antigos</option>
                            <option value="titulo_asc" <?= ($ordem === 'titulo_asc') ? 'selected' : '' ?>>Título (A - Z)</option>
                        </select>
                    </div>

                </div>

            </div>

            <!-- REGRA CORE: FILMES ASSISTIDOS NÃO DEVEM APARECER NAS SUGESTÕES -->
            <?php if ($userId): ?>
                <div class="pt-3 border-t border-[#3d291a] flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-stone-300 hover:text-white select-none">
                        <input 
                            type="checkbox" 
                            name="ocultar_assistidos" 
                            value="1" 
                            <?= $ocultarAssistidos ? 'checked' : '' ?> 
                            onchange="this.form.submit()" 
                            class="w-4 h-4 rounded bg-[#140e09] border-[#3d291a] text-[#b45309] focus:ring-[#e5a93c] cursor-pointer"
                        >
                        <span>Ocultar filmes marcados como <strong class="text-[#e5a93c]">Já Assistido</strong> (recomendação limpa)</span>
                    </label>
                    <a href="meu-espaco.php" class="text-xs text-amber-500 hover:underline">
                        Ver meus assistidos →
                    </a>
                </div>
            <?php endif; ?>

        </form>
    </section>

    <!-- ========================================================================
         GRID DE FILMES BRASILEIROS (Mobile-First Responsivo)
         ======================================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-poster text-2xl text-[#fde68a] tracking-wide">
                    Catálogo de Obras
                </h2>
                <p class="text-xs text-stone-400">
                    Exibindo <?= count($filmes) ?> filmes selecionados
                    <?= !empty($filtroGenero) ? "no gênero '{$filtroGenero}'" : '' ?>
                </p>
            </div>
            
            <?php if ($filtroTipo || $filtroGenero): ?>
                <a href="index.php" class="text-xs text-stone-400 hover:text-[#e5a93c] underline">
                    Limpar todos os filtros
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($filmes)): ?>
            <div class="bg-[#1c140e] border border-[#3d291a] rounded-2xl p-12 text-center max-w-lg mx-auto">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#140e09] flex items-center justify-center text-3xl">
                    🎬
                </div>
                <h3 class="font-serif-vintage text-xl font-bold text-[#fde68a] mb-2">
                    Nenhum filme encontrado
                </h3>
                <p class="text-xs text-stone-400 mb-6">
                    Tente relaxar os filtros aplicados ou desmarcar a opção de ocultar filmes assistidos.
                </p>
                <a href="index.php" class="px-5 py-2.5 rounded-lg bg-[#b45309] text-white text-xs font-bold">
                    Ver Catálogo Completo
                </a>
            </div>
        <?php else: ?>
            <!-- Grid Responsivo Mobile-First: 1 coluna no mobile, 2 em sm, 3 em md, 4 em lg/xl -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($filmes as $filme): ?>
                    <?php include __DIR__ . '/views/movie_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
