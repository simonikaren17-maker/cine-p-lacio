<?php
/**
 * Cine Palácio - Gerador de Cartaz Retrô Anos 70
 * Renderiza um poster vetorial SVG de alta qualidade com estética 1970s / O Agente Secreto
 */

header('Content-Type: image/svg+xml; charset=utf-8');

$title = isset($_GET['title']) ? htmlspecialchars(strip_tags($_GET['title'])) : 'Filme Brasileiro';
$director = isset($_GET['director']) ? htmlspecialchars(strip_tags($_GET['director'])) : 'Cinema Nacional';
$year = isset($_GET['year']) ? htmlspecialchars(strip_tags($_GET['year'])) : '1977';

// Cores da paleta setentista (O Agente Secreto / Cidade de Deus)
$bgDark = '#18120c';
$bgAmber = '#b45309';
$accentYellow = '#e5a93c';
$terracotta = '#8a2524';
$parchment = '#fde68a';
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 750" width="100%" height="100%">
    <defs>
        <!-- Grão e Textura Retrô de Película -->
        <radialGradient id="sunburst" cx="50%" cy="40%" r="65%">
            <stop offset="0%" stop-color="<?= $accentYellow ?>" stop-opacity="0.35"/>
            <stop offset="60%" stop-color="<?= $terracotta ?>" stop-opacity="0.6"/>
            <stop offset="100%" stop-color="<?= $bgDark ?>" stop-opacity="1"/>
        </radialGradient>
        <linearGradient id="band" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="<?= $terracotta ?>"/>
            <stop offset="50%" stop-color="<?= $bgAmber ?>"/>
            <stop offset="100%" stop-color="<?= $accentYellow ?>"/>
        </linearGradient>
    </defs>

    <!-- Fundo de Celuloide -->
    <rect width="500" height="750" fill="<?= $bgDark ?>"/>
    <rect width="470" height="720" x="15" y="15" fill="url(#sunburst)" stroke="<?= $accentYellow ?>" stroke-width="2" opacity="0.9"/>
    
    <!-- Moldura Interna de Cinema Anos 70 -->
    <rect width="450" height="700" x="25" y="25" fill="none" stroke="<?= $terracotta ?>" stroke-width="1.5" stroke-dasharray="10 5" opacity="0.7"/>

    <!-- Elementos Gráficos Geométricos Setentistas -->
    <circle cx="250" cy="280" r="140" fill="none" stroke="<?= $accentYellow ?>" stroke-width="3" opacity="0.4"/>
    <circle cx="250" cy="280" r="100" fill="none" stroke="<?= $terracotta ?>" stroke-width="4" opacity="0.6"/>
    <circle cx="250" cy="280" r="60" fill="url(#band)" opacity="0.8"/>
    
    <!-- Silhueta da Câmera / Rolo 35mm -->
    <path d="M 230 260 L 270 260 L 280 290 L 220 290 Z" fill="<?= $bgDark ?>"/>
    <circle cx="250" cy="275" r="12" fill="<?= $parchment ?>"/>

    <!-- Faixa Superior Retrô -->
    <rect x="35" y="45" width="430" height="34" fill="<?= $terracotta ?>" opacity="0.9"/>
    <text x="250" y="68" font-family="'Courier New', monospace, sans-serif" font-size="14" font-weight="900" letter-spacing="4" fill="<?= $parchment ?>" text-anchor="middle">
        ★ CINE PALÁCIO • APRESENTA ★
    </text>

    <!-- Título do Filme (Tipografia Vintage) -->
    <g transform="translate(250, 470)">
        <text x="0" y="0" font-family="'Georgia', serif" font-size="32" font-weight="900" letter-spacing="2" fill="<?= $parchment ?>" text-anchor="middle" text-transform="uppercase">
            <?= mb_strimwidth($title, 0, 24, '...') ?>
        </text>
        <line x1="-150" y1="20" x2="150" y2="20" stroke="<?= $accentYellow ?>" stroke-width="2"/>
    </g>

    <!-- Diretor e Detalhes de Produção -->
    <text x="250" y="530" font-family="'Courier New', monospace" font-size="14" font-weight="bold" letter-spacing="2" fill="<?= $accentYellow ?>" text-anchor="middle">
        UM FILME DE
    </text>
    <text x="250" y="555" font-family="'Georgia', serif" font-size="18" font-weight="bold" fill="#ffffff" text-anchor="middle">
        <?= mb_strimwidth($director, 0, 30, '...') ?>
    </text>

    <!-- Selo de Película Nacional 35mm -->
    <g transform="translate(250, 630)">
        <rect x="-110" y="-18" width="220" height="36" rx="4" fill="<?= $bgDark ?>" stroke="<?= $accentYellow ?>" stroke-width="1.5"/>
        <text x="0" y="5" font-family="'Courier New', monospace" font-size="12" font-weight="900" letter-spacing="3" fill="<?= $accentYellow ?>" text-anchor="middle">
            CINEMA BRASILEIRO • <?= $year ?>
        </text>
    </g>

    <!-- Rodapé de Classificação e Distribuição -->
    <text x="250" y="695" font-family="sans-serif" font-size="10" letter-spacing="1" fill="#9ca3af" text-anchor="middle">
        DISTRIBUIÇÃO CINEMATOGRÁFICA NACIONAL • BITOLA 35MM
    </text>
</svg>
