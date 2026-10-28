<?php
/**
 * Cine Palácio - Header Principal
 * Configura Tailwind CSS, tipografias dos anos 70 e meta-tags responsivas.
 */

require_once __DIR__ . '/../config/auth.php';
$loggedUser = getLoggedUser();
$pageTitle = isset($pageTitle) ? $pageTitle . ' • Cine Palácio' : 'Cine Palácio • Cinema Brasileiro dos Anos 70 aos Contemporâneos';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Meta SEO & Social -->
    <meta name="description" content="Cine Palácio - O maior acervo interativo do cinema brasileiro. Filtros por mais e menos conhecidos, clássicos dos anos 70, avaliação por estrelas e espaço pessoal.">
    
    <!-- Tailwind CSS (CDN oficial) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cine: {
                            bg: '#120d09',
                            card: '#1c140e',
                            cardHover: '#261b13',
                            border: '#3d291a',
                            amber: '#e5a93c',
                            amberDark: '#b45309',
                            terracotta: '#8a2524',
                            terracottaDark: '#5a1211',
                            parchment: '#fde68a',
                            cream: '#faf5ea',
                            muted: '#a39587'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Estilos Customizados & Fontes Vintage (O Agente Secreto / Cidade de Deus) -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="flex flex-col min-h-screen text-[#faf5ea] antialiased">
