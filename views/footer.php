<?php
/**
 * Cine Palácio - Rodapé Temático Anos 70
 */
require_once __DIR__ . '/toast.php';
require_once __DIR__ . '/modal.php';
?>
<footer class="mt-auto bg-[#0d0906] border-t border-[#3d291a] text-stone-400 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        
        <!-- Coluna 1: Sobre -->
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="font-poster text-2xl text-[#fde68a] tracking-wider">CINE PALÁCIO</span>
            </div>
            <p class="text-xs text-stone-400 leading-relaxed">
                Plataforma dedicada à preservação, catalogação e celebração do Cinema Brasileiro. Da efervescência política e estética dos anos 1970 aos grandes lançamentos contemporâneos.
            </p>
            <div class="text-[11px] font-mono text-[#e5a93c]">
                ★ Sessões Diárias • Película 35mm Digital ★
            </div>
        </div>

        <!-- Coluna 2: Navegação & Filtros -->
        <div>
            <h4 class="font-poster text-sm tracking-wider text-[#fde68a] mb-3 uppercase">Sessões & Mostras</h4>
            <ul class="space-y-2 text-xs">
                <li><a href="index.php" class="hover:text-[#e5a93c] transition-colors">Catálogo Completo</a></li>
                <li><a href="index.php?filtro=mainstream" class="hover:text-[#e5a93c] transition-colors">Filmes Mais Conhecidos</a></li>
                <li><a href="index.php?filtro=cult" class="hover:text-[#e5a93c] transition-colors">Obras Cult & Cinema Marginal</a></li>
                <li><a href="meu-espaco.php" class="hover:text-[#e5a93c] transition-colors">Espaço do Espectador</a></li>
            </ul>
        </div>

        <!-- Coluna 3: Auditoria & Segurança -->
        <div>
            <h4 class="font-poster text-sm tracking-wider text-[#fde68a] mb-3 uppercase">Auditoria & Sistema</h4>
            <ul class="space-y-2 text-xs">
                <li><a href="historico.php" class="hover:text-[#e5a93c] transition-colors">Registro Geral de Auditoria</a></li>
                <li><span class="text-stone-500">Criptografia Bcrypt (Cost 12)</span></li>
                <li><span class="text-stone-500">PDO Prepared Statements Ativos</span></li>
                <li><span class="text-stone-500">Sessões Nativas com HttpOnly</span></li>
            </ul>
        </div>

        <!-- Coluna 4: Referência IMDb & Agente Secreto -->
        <div class="space-y-3">
            <h4 class="font-poster text-sm tracking-wider text-[#fde68a] uppercase">Estética & Referência</h4>
            <p class="text-xs text-stone-400 leading-relaxed">
                Inspirado na identidade visual setentista de <strong class="text-stone-200">O Agente Secreto (2025)</strong> e na energia visceral de <strong class="text-stone-200">Cidade de Deus</strong>. Metadados e catálogo referenciados do <a href="https://www.imdb.com/pt/" target="_blank" rel="noopener" class="text-amber-400 hover:underline">IMDb Brasil</a>.
            </p>
            <div class="pt-2">
                <button type="button" onclick="openImportModal()" class="text-xs px-3 py-1.5 rounded bg-[#1c140e] border border-[#b45309] text-[#fde68a] hover:bg-[#b45309] hover:text-white transition-colors">
                    + Puxar Título do IMDb
                </button>
            </div>
        </div>

    </div>

    <!-- Linha Divisória & Copyright -->
    <div class="max-w-7xl mx-auto pt-6 border-t border-[#261b13] flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-4">
        <div>
            &copy; <?= date('Y') ?> Cine Palácio. Todos os direitos reservados. Preservação do Cinema Nacional.
        </div>
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-stone-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> MySQL PDO Ativo
            </span>
            <span class="inline-flex items-center gap-1.5 text-stone-400">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Sessões Seguras
            </span>
        </div>
    </div>
</footer>

<!-- Scripts Core -->
<script src="assets/js/app.js"></script>
</body>
</html>
