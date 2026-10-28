<?php
/**
 * Cine Palácio - Meu Espaço Personalizado
 * Espaço exclusivo do espectador com listas de filmes que gostou, assistiu e avaliou.
 */

$pageTitle = 'Meu Espaço • Cinemateca Pessoal';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/config/auth.php';

// Exige autenticação para acessar o espaço personalizado
if (!isLoggedIn()) {
    header('Location: login.php?msg=auth_required');
    exit;
}

$pdo = getDBConnection();
$userId = getLoggedUserId();
$user = getLoggedUser();

$aba = $_GET['aba'] ?? 'gostei'; // 'gostei', 'assistidos', 'avaliados'

// Busca Filmes que o usuário Gostou
$stmtGostei = $pdo->prepare("
    SELECT f.*, uf.gostei_em, uf.assistido AS user_assistido, uf.gostei AS user_gostei,
           fa.estrelas AS user_estrelas
    FROM user_filmes uf
    JOIN filmes f ON f.id = uf.filme_id
    LEFT JOIN filme_avaliacoes fa ON fa.filme_id = f.id AND fa.user_id = :uid_fa
    WHERE uf.user_id = :uid AND uf.gostei = 1
    ORDER BY uf.gostei_em DESC
");
$stmtGostei->execute([':uid' => $userId, ':uid_fa' => $userId]);
$filmesGostei = $stmtGostei->fetchAll();

// Busca Filmes que o usuário Já Assistiu
$stmtAssistidos = $pdo->prepare("
    SELECT f.*, uf.assistido_em, uf.assistido AS user_assistido, uf.gostei AS user_gostei,
           fa.estrelas AS user_estrelas
    FROM user_filmes uf
    JOIN filmes f ON f.id = uf.filme_id
    LEFT JOIN filme_avaliacoes fa ON fa.filme_id = f.id AND fa.user_id = :uid_fa
    WHERE uf.user_id = :uid AND uf.assistido = 1
    ORDER BY uf.assistido_em DESC
");
$stmtAssistidos->execute([':uid' => $userId, ':uid_fa' => $userId]);
$filmesAssistidos = $stmtAssistidos->fetchAll();

// Busca Filmes que o usuário Avaliou com Estrelas
$stmtAvaliados = $pdo->prepare("
    SELECT f.*, fa.estrelas AS user_estrelas, fa.updated_at AS avaliado_em,
           uf.assistido AS user_assistido, uf.gostei AS user_gostei
    FROM filme_avaliacoes fa
    JOIN filmes f ON f.id = fa.filme_id
    LEFT JOIN user_filmes uf ON uf.filme_id = f.id AND uf.user_id = :uid_uf
    WHERE fa.user_id = :uid
    ORDER BY fa.updated_at DESC
");
$stmtAvaliados->execute([':uid' => $userId, ':uid_uf' => $userId]);
$filmesAvaliados = $stmtAvaliados->fetchAll();

// Filmes da aba ativa
$filmesExibicao = [];
if ($aba === 'assistidos') {
    $filmesExibicao = $filmesAssistidos;
} elseif ($aba === 'avaliados') {
    $filmesExibicao = $filmesAvaliados;
} else {
    $filmesExibicao = $filmesGostei;
}

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 pb-16">
    
    <!-- Topo do Espaço do Usuário -->
    <div class="bg-gradient-to-b from-[#18120c] via-[#140e09] to-[#120d09] border-b border-[#3d291a] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#b45309] to-[#8a2524] p-1 shadow-2xl flex-shrink-0">
                        <div class="w-full h-full bg-[#1c140e] rounded-xl flex items-center justify-center font-poster text-2xl text-[#fde68a]">
                            <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-poster text-2xl sm:text-3xl text-[#fde68a] tracking-wider">
                                Espaço de <?= htmlspecialchars($user['nome']) ?>
                            </h1>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-[#b45309]/50 border border-[#e5a93c]/40 text-[#fde68a]">
                                Espectador Fiel
                            </span>
                        </div>
                        <p class="text-xs text-stone-400 font-mono mt-0.5">
                            <?= htmlspecialchars($user['email']) ?> • Membro do Cine Palácio
                        </p>
                    </div>
                </div>

                <!-- Estatísticas Rápidas -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-3 text-center min-w-[90px]">
                        <span class="block font-poster text-xl text-rose-500"><?= count($filmesGostei) ?></span>
                        <span class="text-[10px] uppercase font-mono text-stone-400">Gostei</span>
                    </div>
                    <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-3 text-center min-w-[90px]">
                        <span class="block font-poster text-xl text-[#e5a93c]"><?= count($filmesAssistidos) ?></span>
                        <span class="text-[10px] uppercase font-mono text-stone-400">Assistidos</span>
                    </div>
                    <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-3 text-center min-w-[90px]">
                        <span class="block font-poster text-xl text-amber-300"><?= count($filmesAvaliados) ?></span>
                        <span class="text-[10px] uppercase font-mono text-stone-400">Avaliados</span>
                    </div>
                </div>
            </div>

            <!-- Abas de Navegação do Espaço -->
            <div class="flex items-center gap-2 mt-8 border-b border-[#3d291a] overflow-x-auto">
                <a href="meu-espaco.php?aba=gostei" class="pb-3 px-4 text-xs font-bold uppercase tracking-wider flex items-center gap-2 border-b-2 transition-colors whitespace-nowrap <?= ($aba === 'gostei') ? 'border-[#e5a93c] text-[#fde68a]' : 'border-transparent text-stone-400 hover:text-white' ?>">
                    <span>❤️ Filmes que Gostei</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-[#1c140e] text-[10px] border border-[#3d291a]"><?= count($filmesGostei) ?></span>
                </a>
                <a href="meu-espaco.php?aba=assistidos" class="pb-3 px-4 text-xs font-bold uppercase tracking-wider flex items-center gap-2 border-b-2 transition-colors whitespace-nowrap <?= ($aba === 'assistidos') ? 'border-[#e5a93c] text-[#fde68a]' : 'border-transparent text-stone-400 hover:text-white' ?>">
                    <span>👁️ Já Assistidos</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-[#1c140e] text-[10px] border border-[#3d291a]"><?= count($filmesAssistidos) ?></span>
                </a>
                <a href="meu-espaco.php?aba=avaliados" class="pb-3 px-4 text-xs font-bold uppercase tracking-wider flex items-center gap-2 border-b-2 transition-colors whitespace-nowrap <?= ($aba === 'avaliados') ? 'border-[#e5a93c] text-[#fde68a]' : 'border-transparent text-stone-400 hover:text-white' ?>">
                    <span>★ Minhas Avaliações</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-[#1c140e] text-[10px] border border-[#3d291a]"><?= count($filmesAvaliados) ?></span>
                </a>
            </div>

        </div>
    </div>

    <!-- Lista de Filmes da Aba Ativa -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        
        <?php if (empty($filmesExibicao)): ?>
            <div class="bg-[#1c140e] border border-[#3d291a] rounded-2xl p-12 text-center max-w-md mx-auto my-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#140e09] flex items-center justify-center text-3xl">
                    <?php
                    if ($aba === 'assistidos') echo '👁️';
                    elseif ($aba === 'avaliados') echo '★';
                    else echo '❤️';
                    ?>
                </div>
                <h3 class="font-serif-vintage text-xl font-bold text-[#fde68a] mb-2">
                    Sua lista está vazia
                </h3>
                <p class="text-xs text-stone-400 mb-6">
                    <?php
                    if ($aba === 'assistidos') {
                        echo 'Você ainda não marcou nenhum filme como assistido. Ao marcar, eles saem das suas sugestões na página inicial!';
                    } elseif ($aba === 'avaliados') {
                        echo 'Você ainda não avaliou filmes com estrelas. Clique nas estrelas de qualquer cartaz para registrar!';
                    } else {
                        echo 'Você ainda não favoritou nenhum filme. Clique no ícone de coração nos cartazes para guardar suas obras prediletas!';
                    }
                    ?>
                </p>
                <a href="index.php" class="px-5 py-2.5 rounded-lg bg-[#b45309] text-white text-xs font-bold uppercase tracking-wider">
                    Explorar Catálogo
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($filmesExibicao as $filme): ?>
                    <?php include __DIR__ . '/views/movie_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
