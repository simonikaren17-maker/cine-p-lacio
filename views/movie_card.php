<?php
/**
 * Cine Palácio - Componente de Card de Filme
 * Requisitos: Capa, Classificação Indicativa, Sinopse, Estrelas Interativas, Ações Assistido/Gostei.
 */

require_once __DIR__ . '/../backend/actions/poster_finder.php';

// Espera a variável $filme e opcionalmente $userFilmeStatus (assistido, gostei, estrelas_usuario)
if (isset($filme)):
    $capaUrl = resolveMoviePoster($filme['titulo'], $filme['diretor'] ?? '', $filme['capa_url'] ?? null);
    $classificacao = $filme['classificacao_indicativa'] ?? '14';
    
    // Status do usuário logado
    $isWatched = !empty($filme['user_assistido']) && (int)$filme['user_assistido'] === 1;
    $isFavorite = !empty($filme['user_gostei']) && (int)$filme['user_gostei'] === 1;
    $userRating = isset($filme['user_estrelas']) ? (int)$filme['user_estrelas'] : 0;
    
    // Média de estrelas da comunidade
    $avgRating = isset($filme['media_estrelas']) && $filme['media_estrelas'] > 0 
        ? round($filme['media_estrelas'], 1) 
        : round($filme['imdb_rating'] / 2, 1); // Converte 10 para escala 5 se não houver votos locais
?>
<article class="group bg-[#1c140e] border border-[#3d291a] hover:border-[#b45309] rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between">
    
    <!-- Topo: Capa, Classificação Indicativa & Badges -->
    <div class="relative aspect-[2/3] w-full overflow-hidden bg-[#140e09]">
        
        <!-- Capa do Filme com Efeito Zoom Suave -->
        <a href="filme.php?id=<?= $filme['id'] ?>" class="block w-full h-full">
            <img 
                src="<?= htmlspecialchars($capaUrl) ?>" 
                alt="Cartaz de <?= htmlspecialchars($filme['titulo']) ?>"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                onerror="this.onerror=null; this.src='assets/images/retro_poster.php?title=<?= urlencode($filme['titulo']) ?>&director=<?= urlencode($filme['diretor']) ?>&year=<?= $filme['ano'] ?>';"
            />
        </a>

        <!-- Selo Oficial de Classificação Indicativa (Topo Esquerdo) -->
        <div class="absolute top-2.5 left-2.5 z-10" title="Classificação Indicativa: <?= htmlspecialchars($classificacao) ?>">
            <span class="badge-classificacao cl-<?= htmlspecialchars($classificacao) ?>">
                <?= htmlspecialchars($classificacao) ?>
            </span>
        </div>

        <!-- Tag Mais Conhecido / Cult (Topo Direito) -->
        <div class="absolute top-2.5 right-2.5 z-10">
            <?php if (!empty($filme['is_cult'])): ?>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono tracking-wider font-bold bg-[#8a2524]/90 text-[#fde68a] border border-[#e5a93c]/30 shadow backdrop-blur-xs">
                    LADO B • CULT
                </span>
            <?php else: ?>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono tracking-wider font-bold bg-[#b45309]/90 text-white border border-[#fde68a]/30 shadow backdrop-blur-xs">
                    CLÁSSICO NACIONAL
                </span>
            <?php endif; ?>
        </div>

        <!-- Botões de Ação Rápida Flutuantes (Coração & Olho) -->
        <div class="absolute bottom-2.5 right-2.5 z-10 flex items-center gap-1.5 bg-[#120d09]/80 backdrop-blur-md p-1 rounded-lg border border-[#3d291a]">
            <!-- Botão Gostei (Favorito) -->
            <button 
                type="button" 
                class="btn-toggle-favorite p-1.5 rounded transition-colors <?= $isFavorite ? 'text-rose-500' : 'text-stone-400 hover:text-rose-400' ?>"
                data-filme-id="<?= $filme['id'] ?>"
                title="<?= $isFavorite ? 'Remover dos favoritos' : 'Marcar que gostei' ?>"
            >
                <svg class="w-4 h-4" fill="<?= $isFavorite ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>

            <!-- Botão Já assisti -->
            <button 
                type="button" 
                class="btn-toggle-watched p-1.5 rounded transition-colors <?= $isWatched ? 'bg-[#b45309] text-white' : 'text-stone-400 hover:text-white' ?>"
                data-filme-id="<?= $filme['id'] ?>"
                title="<?= $isWatched ? 'Filme assistido (Clique para remover)' : 'Marcar como assistido' ?>"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>

    </div>

    <!-- Corpo: Informações, Sinopse e Avaliação por Estrelas -->
    <div class="p-4 flex-1 flex flex-col justify-between">
        
        <div>
            <!-- Ano & Gênero -->
            <div class="flex items-center justify-between text-xs text-stone-400 mb-1">
                <span class="font-mono text-[#e5a93c] font-bold"><?= htmlspecialchars($filme['ano']) ?></span>
                <span class="truncate max-w-[150px]"><?= htmlspecialchars($filme['generos']) ?></span>
            </div>

            <!-- Título do Filme -->
            <h3 class="font-serif-vintage font-bold text-lg text-[#fde68a] group-hover:text-[#e5a93c] transition-colors leading-tight mb-1">
                <a href="filme.php?id=<?= $filme['id'] ?>">
                    <?= htmlspecialchars($filme['titulo']) ?>
                </a>
            </h3>

            <!-- Diretor -->
            <p class="text-xs text-stone-400 mb-2 truncate">
                Direção: <span class="text-stone-200"><?= htmlspecialchars($filme['diretor'] ?: 'N/A') ?></span>
            </p>

            <!-- Sinopse (truncada com elegância) -->
            <p class="text-xs text-stone-300 line-clamp-2 leading-relaxed mb-3">
                <?= htmlspecialchars($filme['sinopse']) ?>
            </p>
        </div>

        <!-- Rodapé do Card: Estrelas Interativas & Link -->
        <div class="pt-3 border-t border-[#3d291a] flex items-center justify-between gap-2">
            
            <!-- Estrelas Clicáveis (1 a 5) -->
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-mono tracking-wider text-stone-400">Classificar:</span>
                <div class="star-rating star-rating-interactive" data-filme-id="<?= $filme['id'] ?>" data-user-rating="<?= $userRating ?>">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <button 
                            type="button" 
                            class="star-btn <?= ($userRating >= $s || ($userRating === 0 && $avgRating >= $s)) ? 'text-[#e5a93c]' : 'text-stone-600' ?>" 
                            data-star="<?= $s ?>"
                            title="Avaliar com <?= $s ?> estrela(s)"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Botão Ver Detalhes -->
            <a href="filme.php?id=<?= $filme['id'] ?>" class="px-2.5 py-1 rounded bg-[#261b13] hover:bg-[#b45309] text-xs font-semibold text-[#fde68a] hover:text-white transition-colors border border-[#3d291a]">
                Detalhes →
            </a>

        </div>

    </div>

</article>
<?php endif; ?>
