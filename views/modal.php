<?php
/**
 * Cine Palácio - Modal Global Customizado
 * Substitui caixas nativas (alert, confirm) com a identidade do Cine Palácio.
 */
?>
<!-- Backdrop do Modal -->
<div id="custom-modal-backdrop" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-200 hidden opacity-0" role="dialog" aria-modal="true">
    
    <!-- Caixa do Modal Estilizada -->
    <div id="custom-modal-box" class="relative w-full max-w-lg bg-[#1c140e] border-2 border-[#b45309] rounded-xl p-6 sm:p-8 shadow-2xl transform transition-transform duration-200 scale-95 opacity-0 glow-amber text-[#faf5ea]">
        
        <!-- Faixa de Bilheteria / Topo Decorativo -->
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#3d291a]">
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-[#e5a93c] animate-pulse"></span>
                <h3 id="custom-modal-title" class="font-poster text-xl tracking-wider text-[#fde68a]">
                    Cine Palácio Comunica
                </h3>
            </div>
            <button type="button" onclick="closeModal()" class="text-stone-400 hover:text-[#e5a93c] transition-colors p-1" aria-label="Fechar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Conteúdo / Mensagem -->
        <div id="custom-modal-body" class="text-stone-300 text-sm sm:text-base leading-relaxed mb-6">
            <!-- Injetado dinamicamente via JS -->
        </div>

        <!-- Botões de Ação do Modal -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
            <button id="custom-modal-cancel-btn" type="button" class="px-5 py-2.5 rounded-lg border border-[#3d291a] text-stone-300 hover:bg-[#261b13] hover:text-white transition-colors text-sm font-medium hidden">
                Cancelar
            </button>
            <button id="custom-modal-confirm-btn" type="button" class="px-6 py-2.5 rounded-lg bg-[#b45309] hover:bg-[#d97706] text-[#faf5ea] font-bold text-sm tracking-wide shadow-lg transition-all duration-150 transform hover:-translate-y-0.5">
                Entendido
            </button>
        </div>

    </div>
</div>
