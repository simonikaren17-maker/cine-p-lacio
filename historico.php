<?php
/**
 * Cine Palácio - Página de Histórico & Auditoria
 * Requisito CORE: "O sistema deve ter uma página de históricos e manter sempre os logs
 * de qualquer alteração feita por qualquer usuário, para auditorias futuras."
 */

$pageTitle = 'Auditoria & Histórico de Operações';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/audit.php';

$pdo = getDBConnection();

// Filtros de Auditoria
$filtroAcao = trim($_GET['acao'] ?? '');
$busca = trim($_GET['busca'] ?? '');
$pagina = max(1, (int)($_GET['p'] ?? 1));
$limite = 25;
$offset = ($pagina - 1) * $limite;

$filtros = [
    'acao'   => $filtroAcao,
    'busca'  => $busca,
    'limite' => $limite,
    'offset' => $offset
];

$logs = getAuditLogs($pdo, $filtros);
$totalLogs = getAuditLogsCount($pdo, $filtros);
$totalPaginas = max(1, ceil($totalLogs / $limite));

// Lista de ações distintas para o select de filtro
$stmtAcoes = $pdo->query("SELECT DISTINCT acao FROM auditoria_logs ORDER BY acao ASC");
$listaAcoes = $stmtAcoes->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 pb-16">
    
    <!-- Cabeçalho da Auditoria -->
    <div class="bg-gradient-to-b from-[#18120c] via-[#140e09] to-[#120d09] border-b border-[#3d291a] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#8a2524]/60 border border-[#e5a93c]/30 text-[#fde68a] text-xs font-mono uppercase tracking-wider mb-2">
                        <span>🛡️ Livro de Registro & Auditoria</span>
                    </div>
                    <h1 class="font-poster text-3xl sm:text-4xl text-[#fde68a] tracking-wider">
                        Histórico de Auditoria Geral
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-300 font-sans mt-1">
                        Rastreabilidade total: todos os eventos, cadastros, redefinições, avaliações e alterações de catálogo.
                    </p>
                </div>

                <!-- Total de Registros -->
                <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[#b45309]/30 border border-[#e5a93c]/40 flex items-center justify-center text-lg">
                        📜
                    </div>
                    <div>
                        <span class="block text-[10px] font-mono uppercase text-stone-400">Total de Eventos Registrados</span>
                        <span class="font-poster text-2xl text-[#fde68a]"><?= number_format($totalLogs, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros de Auditoria -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <form method="GET" action="historico.php" class="bg-[#1c140e] border border-[#3d291a] rounded-xl p-4 shadow-lg flex flex-col md:flex-row items-center gap-3">
            
            <!-- Campo de Busca Livre (E-mail, Detalhes ou IP) -->
            <div class="flex-1 w-full relative">
                <input 
                    type="text" 
                    name="busca" 
                    value="<?= htmlspecialchars($busca) ?>" 
                    placeholder="Buscar por e-mail, texto dos detalhes ou IP de origem..." 
                    class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg pl-9 pr-4 py-2 text-xs text-[#faf5ea] placeholder-stone-500 focus:outline-none"
                >
                <svg class="w-4 h-4 text-stone-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Select de Tipo de Ação -->
            <div class="w-full md:w-56">
                <select name="acao" class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] text-xs text-[#faf5ea] rounded-lg px-3 py-2 focus:outline-none">
                    <option value="">Todas as Ações</option>
                    <?php foreach ($listaAcoes as $act): ?>
                        <option value="<?= htmlspecialchars($act) ?>" <?= ($filtroAcao === $act) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($act) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-initial px-4 py-2 rounded-lg bg-[#b45309] hover:bg-[#d97706] text-white font-bold text-xs uppercase tracking-wider transition-colors shadow">
                    Filtrar
                </button>
                <?php if ($busca || $filtroAcao): ?>
                    <a href="historico.php" class="px-3 py-2 rounded-lg border border-[#3d291a] text-xs text-stone-400 hover:text-white transition-colors">
                        Limpar
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </div>

    <!-- Tabela Responsiva de Logs de Auditoria -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-[#1c140e] border border-[#3d291a] rounded-xl overflow-hidden shadow-xl">
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    
                    <thead class="bg-[#140e09] text-stone-400 font-mono uppercase tracking-wider border-b border-[#3d291a]">
                        <tr>
                            <th class="py-3 px-4">Data / Hora</th>
                            <th class="py-3 px-4">Ação</th>
                            <th class="py-3 px-4">Usuário / E-mail</th>
                            <th class="py-3 px-4">Detalhes da Alteração</th>
                            <th class="py-3 px-4">IP / Origem</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#3d291a] text-stone-300">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="py-8 px-4 text-center text-stone-500">
                                    Nenhum log de auditoria encontrado para os critérios selecionados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): 
                                $badgeStyle = getActionBadgeStyle($log['acao']);
                                $dataFmt = date('d/m/Y H:i:s', strtotime($log['criado_em']));
                            ?>
                                <tr class="hover:bg-[#261b13] transition-colors">
                                    
                                    <!-- Data / Hora -->
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-stone-400">
                                        <?= $dataFmt ?>
                                    </td>

                                    <!-- Ação com Badge Estilizado -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold <?= $badgeStyle ?>">
                                            <?= htmlspecialchars($log['acao']) ?>
                                        </span>
                                    </td>

                                    <!-- Usuário -->
                                    <td class="py-3.5 px-4 whitespace-nowrap font-medium text-[#fde68a]">
                                        <?= htmlspecialchars($log['user_email'] ?: 'Visitante Anônimo') ?>
                                    </td>

                                    <!-- Detalhes -->
                                    <td class="py-3.5 px-4 text-stone-200">
                                        <?= htmlspecialchars($log['detalhes'] ?: '—') ?>
                                    </td>

                                    <!-- IP e User-Agent -->
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-stone-400" title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                        <?= htmlspecialchars($log['ip_origem'] ?: '127.0.0.1') ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
                <div class="p-4 border-t border-[#3d291a] flex items-center justify-between text-xs text-stone-400">
                    <div>
                        Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalLogs ?> registros)
                    </div>
                    <div class="flex items-center gap-1">
                        <?php if ($pagina > 1): ?>
                            <a href="historico.php?p=<?= $pagina - 1 ?>&acao=<?= urlencode($filtroAcao) ?>&busca=<?= urlencode($busca) ?>" class="px-3 py-1.5 rounded bg-[#140e09] border border-[#3d291a] hover:bg-[#261b13] text-stone-300">
                                ← Anterior
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="historico.php?p=<?= $pagina + 1 ?>&acao=<?= urlencode($filtroAcao) ?>&busca=<?= urlencode($busca) ?>" class="px-3 py-1.5 rounded bg-[#140e09] border border-[#3d291a] hover:bg-[#261b13] text-stone-300">
                                Próxima →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
/**
 * Retorna as classes CSS de cor do badge conforme a ação
 */
function getActionBadgeStyle(string $acao): string {
    switch ($acao) {
        case 'CADASTRO':
            return 'bg-emerald-950 text-emerald-300 border border-emerald-800';
        case 'LOGIN':
            return 'bg-blue-950 text-blue-300 border border-blue-800';
        case 'LOGIN_FALHA':
            return 'bg-rose-950 text-rose-300 border border-rose-800';
        case 'LOGOUT':
            return 'bg-stone-900 text-stone-400 border border-stone-700';
        case 'REDEFINICAO_SENHA':
            return 'bg-amber-950 text-amber-300 border border-amber-800';
        case 'AVALIAR_FILME':
            return 'bg-amber-900/60 text-[#fde68a] border border-[#e5a93c]/50';
        case 'FAVORITAR':
            return 'bg-pink-950 text-pink-300 border border-pink-800';
        case 'DESFAVORITAR':
            return 'bg-stone-800 text-stone-400 border border-stone-600';
        case 'MARCAR_ASSISTIDO':
            return 'bg-[#8a2524]/60 text-orange-200 border border-orange-800';
        case 'DESMARCAR_ASSISTIDO':
            return 'bg-stone-800 text-stone-400 border border-stone-600';
        case 'IMPORTAR_IMDB':
        case 'SEED_FILMES':
            return 'bg-yellow-950 text-yellow-300 border border-yellow-700';
        default:
            return 'bg-stone-800 text-stone-300 border border-stone-700';
    }
}

require_once __DIR__ . '/views/footer.php';
?>
