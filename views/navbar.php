<?php
/**
 * Cine Palácio - Barra de Navegação Responsiva (Mobile-First)
 */
require_once __DIR__ . '/../config/auth.php';
$user = getLoggedUser();
$currentScript = basename($_SERVER['PHP_SELF']);
$currentFiltro = $_GET['filtro'] ?? '';
?>
<header class="sticky top-0 z-40 bg-[#120d09]/95 backdrop-blur-md border-b border-[#3d291a] shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
            
            <!-- Marca / Letreiro Cine Palácio -->
            <a href="index.php" class="flex items-center gap-3 group flex-shrink-0">
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#b45309] to-[#8a2524] p-0.5 shadow-lg group-hover:scale-105 transition-transform duration-200">
                    <div class="w-full h-full bg-[#1c140e] rounded-md flex items-center justify-center border border-[#e5a93c]/30">
                        <span class="font-poster text-2xl text-[#fde68a]">CP</span>
                    </div>
                </div>
                <div>
                    <span class="block font-poster text-2xl sm:text-3xl tracking-widest text-[#fde68a] group-hover:text-[#e5a93c] transition-colors leading-none">
                        CINE PALÁCIO
                    </span>
                    <span class="block text-[10px] sm:text-xs font-mono tracking-widest text-[#a39587] uppercase">
                        Cinema Brasileiro • Est. 1970
                    </span>
                </div>
            </a>

            <!-- Barra de Busca Dinâmica (Desktop / Tablet) -->
            <div class="hidden md:block flex-1 max-w-md mx-4 relative">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Buscar por filme, diretor, ator ou gênero..." 
                        class="live-search-input w-full bg-[#1c140e] border border-[#3d291a] focus:border-[#e5a93c] rounded-full py-2.5 pl-10 pr-4 text-sm text-[#faf5ea] placeholder-stone-500 focus:outline-none focus:ring-1 focus:ring-[#e5a93c] transition-all shadow-inner"
                        autocomplete="off"
                    >
                    <svg class="w-4 h-4 text-stone-500 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <!-- Dropdown de Resultados Dinâmicos -->
                    <div class="live-search-results absolute left-0 right-0 mt-2 bg-[#1c140e] border border-[#b45309] rounded-xl shadow-2xl overflow-hidden hidden z-50"></div>
                </div>
            </div>

            <!-- Navegação Desktop -->
            <nav class="hidden lg:flex items-center gap-6 text-sm font-medium">
                <a href="index.php" class="transition-colors hover:text-[#e5a93c] <?= ($currentScript === 'index.php' && empty($currentFiltro)) ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
                    Descobrir
                </a>
                <a href="index.php?filtro=mainstream" class="transition-colors hover:text-[#e5a93c] <?= ($currentFiltro === 'mainstream') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
                    Mais Conhecidos
                </a>
                <a href="index.php?filtro=cult" class="transition-colors hover:text-[#e5a93c] <?= ($currentFiltro === 'cult') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
                    Lado B / Cult
                </a>
                <a href="meu-espaco.php" class="transition-colors hover:text-[#e5a93c] <?= ($currentScript === 'meu-espaco.php') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
                    Meu Espaço
                </a>
                <a href="historico.php" class="transition-colors hover:text-[#e5a93c] <?= ($currentScript === 'historico.php') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
                    Auditoria
                </a>
            </nav>

            <!-- Ações do Usuário & Autenticação -->
            <div class="hidden sm:flex items-center gap-3">
                <!-- Botão de Importar do IMDb -->
                <button type="button" onclick="openImportModal()" class="px-3 py-1.5 rounded-lg border border-[#3d291a] hover:border-[#b45309] bg-[#1c140e] text-xs text-[#fde68a] hover:text-[#e5a93c] transition-colors flex items-center gap-1.5" title="Importar filme do IMDb">
                    <span class="font-bold text-[10px] bg-[#f5c518] text-black px-1 rounded">IMDb</span>
                    <span>+ Adicionar</span>
                </button>

                <?php if ($user): ?>
                    <div class="flex items-center gap-3 pl-2 border-l border-[#3d291a]">
                        <a href="meu-espaco.php" class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-full bg-[#8a2524] border border-[#e5a93c]/40 flex items-center justify-center font-bold text-xs text-[#fde68a] shadow">
                                <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                            </div>
                            <span class="text-xs text-stone-300 group-hover:text-[#fde68a] transition-colors max-w-[120px] truncate">
                                <?= htmlspecialchars($user['nome']) ?>
                            </span>
                        </a>
                        <button type="button" onclick="confirmLogout()" class="p-1.5 text-stone-400 hover:text-rose-400 transition-colors" title="Sair da conta">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-2">
                        <a href="login.php" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-stone-300 hover:text-white hover:bg-[#1c140e] transition-colors">
                            Entrar
                        </a>
                        <a href="cadastro.php" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#b45309] hover:bg-[#d97706] text-[#faf5ea] shadow-md transition-all">
                            Cadastrar
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Botão Menu Mobile (Hambúrguer) -->
            <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-lg text-stone-400 hover:text-[#e5a93c] hover:bg-[#1c140e] transition-colors" aria-label="Abrir menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

        </div>

        <!-- Barra de Busca Dinâmica Mobile -->
        <div class="md:hidden pb-4 relative">
            <input 
                type="text" 
                placeholder="Buscar filmes, diretores..." 
                class="live-search-input w-full bg-[#1c140e] border border-[#3d291a] focus:border-[#e5a93c] rounded-full py-2 pl-9 pr-4 text-xs text-[#faf5ea] placeholder-stone-500 focus:outline-none shadow-inner"
                autocomplete="off"
            >
            <svg class="w-4 h-4 text-stone-500 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <div class="live-search-results absolute left-0 right-0 mt-1 bg-[#1c140e] border border-[#b45309] rounded-xl shadow-2xl overflow-hidden hidden z-50"></div>
        </div>

    </div>

    <!-- Gaveta Menu Mobile -->
    <div id="mobile-menu" class="hidden lg:hidden bg-[#18120c] border-b border-[#3d291a] px-4 pt-2 pb-6 space-y-3">
        <a href="index.php" class="block py-2 text-sm <?= ($currentScript === 'index.php' && empty($currentFiltro)) ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
            ★ Descobrir
        </a>
        <a href="index.php?filtro=mainstream" class="block py-2 text-sm <?= ($currentFiltro === 'mainstream') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
            🎬 Mais Conhecidos
        </a>
        <a href="index.php?filtro=cult" class="block py-2 text-sm <?= ($currentFiltro === 'cult') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
            📼 Lado B / Cult
        </a>
        <a href="meu-espaco.php" class="block py-2 text-sm <?= ($currentScript === 'meu-espaco.php') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
            ❤️ Meu Espaço
        </a>
        <a href="historico.php" class="block py-2 text-sm <?= ($currentScript === 'historico.php') ? 'text-[#e5a93c] font-bold' : 'text-stone-300' ?>">
            📜 Auditoria & Histórico
        </a>
        
        <div class="pt-4 border-t border-[#3d291a] flex flex-col gap-2">
            <button type="button" onclick="openImportModal()" class="w-full py-2.5 rounded-lg border border-[#b45309] text-xs text-[#fde68a] font-bold flex items-center justify-center gap-2">
                <span class="bg-[#f5c518] text-black px-1 rounded">IMDb</span> + Importar Filme
            </button>
            <?php if ($user): ?>
                <div class="py-2 text-xs text-stone-400">
                    Conectado como <strong class="text-[#fde68a]"><?= htmlspecialchars($user['email']) ?></strong>
                </div>
                <button type="button" onclick="confirmLogout()" class="w-full py-2.5 rounded-lg bg-[#8a2524] text-white text-xs font-bold">
                    Sair da Conta
                </button>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="login.php" class="text-center py-2.5 rounded-lg border border-[#3d291a] text-xs text-stone-200">
                        Entrar
                    </a>
                    <a href="cadastro.php" class="text-center py-2.5 rounded-lg bg-[#b45309] text-xs text-white font-bold">
                        Cadastrar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

function confirmLogout() {
    window.showModal({
        title: 'Sair do Cine Palácio',
        message: 'Tem certeza de que deseja encerrar sua sessão?',
        confirmText: 'Sim, Sair',
        cancelText: 'Continuar no Cine',
        onConfirm: () => {
            window.location.href = 'backend/actions/logout.php';
        }
    });
}

function openImportModal() {
    const content = `
        <div class="space-y-4">
            <p class="text-xs text-stone-400 leading-relaxed">
                Puxe qualquer filme brasileiro diretamente da base de referência do IMDb (<a href="https://www.imdb.com/pt/" target="_blank" class="text-amber-400 underline">imdb.com/pt</a>). Se a capa não for fornecida pelo IMDb, nosso sistema procurará pelo título e diretor automaticamente!
            </p>
            <div>
                <label class="block text-xs font-bold text-[#fde68a] mb-1">ID do Filme no IMDb ou URL:</label>
                <input type="text" id="imdb-input-id" placeholder="Ex: tt0317248 ou https://www.imdb.com/pt/title/tt0317248/" class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg px-3 py-2 text-xs text-white focus:outline-none">
            </div>
            <div id="imdb-feedback" class="text-xs hidden"></div>
        </div>
    `;

    window.showModal({
        title: '★ Importar Referência do IMDb ★',
        message: content,
        confirmText: 'Importar para a Base',
        cancelText: 'Cancelar',
        onConfirm: () => {
            const input = document.getElementById('imdb-input-id');
            const val = input ? input.value.trim() : '';
            if (!val) {
                window.showToast('Informe o ID ou URL do IMDb!', 'warning');
                return;
            }

            window.showToast('Consultando IMDb e catalogando filme...', 'info');

            const fd = new FormData();
            fd.append('imdb_url_or_id', val);

            fetch('backend/actions/import_imdb.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.showToast(res.message, 'success');
                    if (res.redirect) {
                        setTimeout(() => window.location.href = res.redirect, 1000);
                    }
                } else {
                    window.showToast(res.message || 'Erro ao importar.', 'error');
                }
            })
            .catch(() => window.showToast('Erro de conexão ao contatar o IMDb.', 'error'));
        }
    });
}
</script>
