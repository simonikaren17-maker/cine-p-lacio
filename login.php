<?php
/**
 * Cine Palácio - Página de Login
 */

$pageTitle = 'Entrar na Sala • Cine Palácio';
require_once __DIR__ . '/config/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-md space-y-6">
        
        <!-- Cartaz de Bilheteria / Cabeçalho -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#b45309] to-[#8a2524] p-0.5 shadow-xl mb-3">
                <div class="w-full h-full bg-[#1c140e] rounded-xl flex items-center justify-center font-poster text-2xl text-[#fde68a]">
                    CP
                </div>
            </div>
            <h1 class="font-poster text-3xl text-[#fde68a] tracking-wider uppercase">
                Acesse Sua Poltrona
            </h1>
            <p class="text-xs text-stone-400 mt-1 font-mono">
                Identifique-se para gerenciar seus filmes e avaliações.
            </p>
        </div>

        <!-- Formulário Estilizado Estilo Cinema Anos 70 -->
        <div class="bg-[#1c140e] border-2 border-[#b45309] rounded-2xl p-6 sm:p-8 shadow-2xl glow-amber">
            
            <form id="form-login" class="space-y-4" onsubmit="handleLoginSubmit(event)">
                
                <div>
                    <label for="email" class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">
                        E-mail de Acesso
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="seu@email.com" 
                        class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg px-3.5 py-2.5 text-sm text-[#faf5ea] placeholder-stone-600 focus:outline-none focus:ring-1 focus:ring-[#e5a93c] transition-colors"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] font-bold">
                            Senha
                        </label>
                        <a href="recuperar-senha.php" class="text-xs text-stone-400 hover:text-[#fde68a] transition-colors">
                            Esqueceu a senha?
                        </a>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••" 
                        class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg px-3.5 py-2.5 text-sm text-[#faf5ea] placeholder-stone-600 focus:outline-none focus:ring-1 focus:ring-[#e5a93c] transition-colors"
                    >
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        id="btn-login-submit"
                        class="w-full py-3 rounded-lg bg-[#b45309] hover:bg-[#d97706] text-[#faf5ea] font-poster text-lg tracking-wider uppercase shadow-xl transition-all duration-200 transform hover:-translate-y-0.5"
                    >
                        Entrar na Sala
                    </button>
                </div>

            </form>

            <!-- Rodapé do Card com Link para Cadastro -->
            <div class="mt-6 pt-5 border-t border-[#3d291a] text-center text-xs text-stone-400">
                Ainda não tem cadastro? 
                <a href="cadastro.php" class="font-bold text-[#fde68a] hover:text-[#e5a93c] transition-colors ml-1">
                    Cadastre-se grátis →
                </a>
            </div>

            <!-- Dica de Usuário Demonstração -->
            <div class="mt-4 p-3 rounded-lg bg-[#140e09] border border-[#3d291a] text-[11px] text-stone-400">
                <span class="text-[#e5a93c] font-bold">Acesso rápido demonstração:</span><br>
                E-mail: <code class="text-stone-300">cinefilo@cinepalacio.br</code><br>
                Senha: <code class="text-stone-300">cinema123</code>
            </div>

        </div>

    </div>

</main>

<script>
function handleLoginSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-login-submit');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Verificando...';

    const formData = new FormData(e.target);

    fetch('backend/actions/login.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect || 'index.php';
            }, 800);
        } else {
            btn.disabled = false;
            btn.textContent = originalText;
            window.showToast(data.message || 'Falha ao efetuar login.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = originalText;
        window.showToast('Erro de conexão com o servidor.', 'error');
    });
}
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
