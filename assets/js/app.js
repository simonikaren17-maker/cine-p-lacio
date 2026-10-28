/**
 * Cine Palácio - Core JavaScript
 * Trata Toasts, Modais Personalizados, Busca Dinâmica, Avaliação por Estrelas e Ações do Usuário.
 */

document.addEventListener('DOMContentLoaded', () => {
    initLiveSearch();
    initRatingStars();
    initUserActionButtons();
    checkUrlMessages();
});

/* ==========================================================================
   SISTEMA DE TOASTS (Notificações Flutuantes Não Bloqueantes)
   ========================================================================== */
window.showToast = function(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `transform transition-all duration-300 ease-out translate-y-2 opacity-0 flex items-start gap-3 p-4 rounded-lg shadow-2xl border text-sm max-w-md w-full pointer-events-auto`;

    // Paleta visual inspirada nos anos 70
    let bgBorder = 'bg-[#1c140e] border-[#b45309] text-[#fde68a]';
    let iconSvg = `
        <svg class="w-5 h-5 text-[#e5a93c] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>`;

    if (type === 'success') {
        bgBorder = 'bg-[#182216] border-[#22c55e] text-[#dcfce7]';
        iconSvg = `
            <svg class="w-5 h-5 text-[#22c55e] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>`;
    } else if (type === 'error') {
        bgBorder = 'bg-[#291010] border-[#8a2524] text-[#fecaca]';
        iconSvg = `
            <svg class="w-5 h-5 text-[#ef4444] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>`;
    } else if (type === 'warning') {
        bgBorder = 'bg-[#2c1d0c] border-[#f59e0b] text-[#fef3c7]';
        iconSvg = `
            <svg class="w-5 h-5 text-[#f59e0b] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>`;
    }

    toast.className += ` ${bgBorder}`;
    toast.innerHTML = `
        ${iconSvg}
        <div class="flex-1 font-medium">${message}</div>
        <button type="button" class="text-stone-400 hover:text-white transition-colors" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;

    container.appendChild(toast);

    // Animação de entrada
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Auto-remover
    setTimeout(() => {
        toast.classList.remove('opacity-100', 'translate-y-0');
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

/* ==========================================================================
   MODAL PERSONALIZADO (Substitui caixas de mensagens nativas do navegador)
   ========================================================================== */
window.showModal = function({
    title = 'Cine Palácio Informa',
    message = '',
    type = 'info',
    confirmText = 'Entendido',
    cancelText = null,
    onConfirm = null,
    onCancel = null
}) {
    const modalBackdrop = document.getElementById('custom-modal-backdrop');
    const modalBox = document.getElementById('custom-modal-box');
    const modalTitle = document.getElementById('custom-modal-title');
    const modalBody = document.getElementById('custom-modal-body');
    const modalConfirmBtn = document.getElementById('custom-modal-confirm-btn');
    const modalCancelBtn = document.getElementById('custom-modal-cancel-btn');

    if (!modalBackdrop || !modalBox) {
        // Fallback seguro caso o elemento modal não esteja presente
        console.warn('Modal container não encontrado no DOM');
        if (confirmText && onConfirm) onConfirm();
        return;
    }

    modalTitle.textContent = title;
    modalBody.innerHTML = message;
    modalConfirmBtn.textContent = confirmText;

    if (cancelText) {
        modalCancelBtn.textContent = cancelText;
        modalCancelBtn.classList.remove('hidden');
    } else {
        modalCancelBtn.classList.add('hidden');
    }

    // Configura callbacks
    modalConfirmBtn.onclick = () => {
        closeModal();
        if (typeof onConfirm === 'function') onConfirm();
    };

    modalCancelBtn.onclick = () => {
        closeModal();
        if (typeof onCancel === 'function') onCancel();
    };

    // Abre modal com transição suave
    modalBackdrop.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalBackdrop.classList.remove('opacity-0');
        modalBackdrop.classList.add('opacity-100');
        modalBox.classList.remove('scale-95', 'opacity-0');
        modalBox.classList.add('scale-100', 'opacity-100');
    });
};

window.closeModal = function() {
    const modalBackdrop = document.getElementById('custom-modal-backdrop');
    const modalBox = document.getElementById('custom-modal-box');
    if (!modalBackdrop) return;

    modalBackdrop.classList.remove('opacity-100');
    modalBackdrop.classList.add('opacity-0');
    modalBox.classList.remove('scale-100', 'opacity-100');
    modalBox.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modalBackdrop.classList.add('hidden');
    }, 200);
};

/* ==========================================================================
   BUSCA DINÂMICA COM AUTOCOMPLETE (Live Search)
   ========================================================================== */
function initLiveSearch() {
    const searchInputs = document.querySelectorAll('.live-search-input');
    
    searchInputs.forEach(input => {
        const resultsBox = input.parentElement.querySelector('.live-search-results');
        if (!resultsBox) return;

        let debounceTimer;

        input.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`backend/api/search.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        renderSearchResults(data.results || [], resultsBox, query);
                    })
                    .catch(err => {
                        console.error('Erro na busca dinâmica:', err);
                    });
            }, 250);
        });

        // Fechar ao clicar fora
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });

        input.addEventListener('focus', () => {
            if (resultsBox.children.length > 0 && input.value.trim().length >= 2) {
                resultsBox.classList.remove('hidden');
            }
        });
    });
}

function renderSearchResults(results, container, query) {
    if (results.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center text-stone-400 text-sm">
                Nenhum filme encontrado para "<span class="text-amber-400">${query}</span>".
            </div>
        `;
        container.classList.remove('hidden');
        return;
    }

    let html = '<div class="divide-y divide-[#3d291a] max-h-96 overflow-y-auto">';
    results.forEach(movie => {
        const badgeColor = getClassificacaoColor(movie.classificacao);
        html += `
            <a href="${movie.url}" class="flex items-center gap-3 p-3 hover:bg-[#261b13] transition-colors group">
                <img src="${movie.capa_url}" alt="${movie.titulo}" class="w-10 h-14 object-cover rounded shadow border border-[#3d291a] flex-shrink-0" onerror="this.src='assets/images/retro_poster.php?title=${encodeURIComponent(movie.titulo)}'"/>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm text-[#fde68a] group-hover:text-[#e5a93c] transition-colors truncate">${movie.titulo}</span>
                        <span class="text-xs px-1.5 py-0.2 rounded font-bold text-white ${badgeColor}">${movie.classificacao}</span>
                    </div>
                    <div class="text-xs text-stone-400 truncate mt-0.5">${movie.ano} • Dir: ${movie.diretor || 'N/A'}</div>
                    <div class="text-xs text-amber-500 flex items-center gap-1 mt-0.5">
                        <span>★ ${movie.rating}</span>
                        <span class="text-stone-500">•</span>
                        <span class="text-stone-400 truncate">${movie.generos}</span>
                    </div>
                </div>
            </a>
        `;
    });
    html += '</div>';

    container.innerHTML = html;
    container.classList.remove('hidden');
}

function getClassificacaoColor(classificacao) {
    switch (String(classificacao).toUpperCase()) {
        case 'L':
        case 'LIVRE': return 'bg-[#00a859]';
        case '10': return 'bg-[#0095da]';
        case '12': return 'bg-[#ffcc00] text-black';
        case '14': return 'bg-[#f58220]';
        case '16': return 'bg-[#ee1c24]';
        case '18': return 'bg-[#000000] border border-stone-600';
        default: return 'bg-stone-700';
    }
}

/* ==========================================================================
   CLASSIFICAÇÃO DE FILMES POR ESTRELAS (1 a 5)
   ========================================================================== */
function initRatingStars() {
    document.querySelectorAll('.star-rating-interactive').forEach(container => {
        const buttons = container.querySelectorAll('.star-btn');
        const filmeId = container.dataset.filmeId;

        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const stars = parseInt(btn.dataset.star, 10);
                rateMovie(filmeId, stars, container);
            });

            // Efeito visual no hover
            btn.addEventListener('mouseenter', () => {
                const hoverStar = parseInt(btn.dataset.star, 10);
                buttons.forEach(b => {
                    const bStar = parseInt(b.dataset.star, 10);
                    if (bStar <= hoverStar) {
                        b.classList.add('text-[#e5a93c]');
                    } else {
                        b.classList.remove('text-[#e5a93c]');
                    }
                });
            });
        });

        container.addEventListener('mouseleave', () => {
            const currentRating = parseInt(container.dataset.userRating || 0, 10);
            buttons.forEach(b => {
                const bStar = parseInt(b.dataset.star, 10);
                if (bStar <= currentRating) {
                    b.classList.add('text-[#e5a93c]');
                } else {
                    b.classList.remove('text-[#e5a93c]');
                }
            });
        });
    });
}

function rateMovie(filmeId, stars, container) {
    const formData = new FormData();
    formData.append('filme_id', filmeId);
    formData.append('estrelas', stars);

    fetch('backend/api/rate.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.require_login) {
            window.showModal({
                title: 'Entrar no Cine Palácio',
                message: 'Você precisa estar logado para avaliar filmes e salvar no seu espaço!',
                confirmText: 'Fazer Login',
                cancelText: 'Mais tarde',
                onConfirm: () => {
                    window.location.href = 'login.php';
                }
            });
            return;
        }

        if (data.success) {
            container.dataset.userRating = stars;
            const buttons = container.querySelectorAll('.star-btn');
            buttons.forEach(b => {
                const bStar = parseInt(b.dataset.star, 10);
                if (bStar <= stars) {
                    b.classList.add('text-[#e5a93c]');
                } else {
                    b.classList.remove('text-[#e5a93c]');
                }
            });

            // Atualiza texto de média na tela se existir
            const avgBadge = document.querySelector(`.filme-rating-avg-${filmeId}`);
            if (avgBadge && data.media_estrelas) {
                avgBadge.textContent = `${data.media_estrelas} (${data.total_votos} votos)`;
            }

            window.showToast(data.message, 'success');
        } else {
            window.showToast(data.message || 'Erro ao registrar avaliação.', 'error');
        }
    })
    .catch(err => {
        console.error('Erro na avaliação:', err);
        window.showToast('Erro de conexão ao enviar avaliação.', 'error');
    });
}

/* ==========================================================================
   BOTÕES DE AÇÃO: JÁ ASSISTI & GOSTEI (FAVORITOS)
   ========================================================================== */
function initUserActionButtons() {
    // Ação: Já assisti
    document.querySelectorAll('.btn-toggle-watched').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const filmeId = btn.dataset.filmeId;

            const formData = new FormData();
            formData.append('filme_id', filmeId);

            fetch('backend/api/toggle_watched.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.require_login) {
                    window.showModal({
                        title: 'Acesse seu Espaço',
                        message: 'Crie sua conta ou faça login para marcar filmes assistidos e personalizar suas recomendações!',
                        confirmText: 'Entrar Agora',
                        cancelText: 'Cancelar',
                        onConfirm: () => window.location.href = 'login.php'
                    });
                    return;
                }

                if (data.success) {
                    if (data.assistido) {
                        btn.classList.add('bg-[#b45309]', 'text-white');
                        btn.classList.remove('bg-stone-800', 'text-stone-300');
                        btn.setAttribute('title', 'Filme Assistido (Clique para remover)');
                    } else {
                        btn.classList.remove('bg-[#b45309]', 'text-white');
                        btn.classList.add('bg-stone-800', 'text-stone-300');
                        btn.setAttribute('title', 'Marcar como Assistido');
                    }
                    window.showToast(data.message, 'success');
                } else {
                    window.showToast(data.message, 'error');
                }
            })
            .catch(err => {
                window.showToast('Erro ao atualizar status de assistido.', 'error');
            });
        });
    });

    // Ação: Gostei / Favorito
    document.querySelectorAll('.btn-toggle-favorite').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const filmeId = btn.dataset.filmeId;

            const formData = new FormData();
            formData.append('filme_id', filmeId);

            fetch('backend/api/toggle_favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.require_login) {
                    window.showModal({
                        title: 'Filmes Favoritos',
                        message: 'Cadastre-se para favoritar obras do cinema brasileiro e montar sua cinemateca pessoal!',
                        confirmText: 'Cadastrar / Entrar',
                        cancelText: 'Depois',
                        onConfirm: () => window.location.href = 'cadastro.php'
                    });
                    return;
                }

                if (data.success) {
                    const heartSvg = btn.querySelector('svg');
                    if (data.gostei) {
                        btn.classList.add('text-rose-500');
                        btn.classList.remove('text-stone-400');
                        if (heartSvg) heartSvg.setAttribute('fill', 'currentColor');
                    } else {
                        btn.classList.remove('text-rose-500');
                        btn.classList.add('text-stone-400');
                        if (heartSvg) heartSvg.setAttribute('fill', 'none');
                    }
                    window.showToast(data.message, 'success');
                } else {
                    window.showToast(data.message, 'error');
                }
            })
            .catch(err => {
                window.showToast('Erro ao atualizar favoritos.', 'error');
            });
        });
    });
}

/* ==========================================================================
   FEEDBACK DE PARÂMETROS URL (?msg=...)
   ========================================================================== */
function checkUrlMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'logged_out') {
        window.showToast('Você saiu da sua conta. Até a próxima sessão no Cine Palácio!', 'info');
    } else if (msg === 'auth_required') {
        window.showToast('Por favor, faça login para acessar este espaço.', 'warning');
    } else if (msg === 'initialized') {
        window.showToast('Banco de dados e acervo do Cine Palácio inicializados com sucesso!', 'success');
    }
}
