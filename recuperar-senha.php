<?php
/**
 * Cine Palácio - Redefinição de Senha
 * Requisito: "o site deve dar a opção de redefinição de senha para o usuario."
 */

$pageTitle = 'Redefinição de Senha • Cine Palácio';
require_once __DIR__ . '/config/auth.php';

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/navbar.php';
?>

<main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-md space-y-6">
        
        <!-- Cabeçalho -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#b45309] to-[#8a2524] p-0.5 shadow-xl mb-3">
                <div class="w-full h-full bg-[#1c140e] rounded-xl flex items-center justify-center font-poster text-2xl text-[#fde68a]">
                    🔑
                </div>
            </div>
            <h1 class="font-poster text-3xl text-[#fde68a] tracking-wider uppercase">
                Redefinir Senha
            </h1>
            <p class="text-xs text-stone-400 mt-1 font-mono">
                Atualize sua credencial com criptografia segura Bcrypt.
            </p>
        </div>

        <!-- Formulário de Redefinição de Senha -->
        <div class="bg-[#1c140e] border-2 border-[#b45309] rounded-2xl p-6 sm:p-8 shadow-2xl glow-amber">
            
            <form id="form-reset" class="space-y-4" onsubmit="handleResetSubmit(event)">
                
                <div>
                    <label for="email" class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">
                        E-mail Cadastrado
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
                    <label for="new_password" class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">
                        Nova Senha (Mínimo 6 caracteres)
                    </label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required 
                        minlength="6"
                        placeholder="••••••••" 
                        class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg px-3.5 py-2.5 text-sm text-[#faf5ea] placeholder-stone-600 focus:outline-none focus:ring-1 focus:ring-[#e5a93c] transition-colors"
                    >
                </div>

                <div>
                    <label for="confirm_password" class="block text-xs font-mono uppercase tracking-wider text-[#e5a93c] mb-1 font-bold">
                        Confirme a Nova Senha
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        minlength="6"
                        placeholder="••••••••" 
                        class="w-full bg-[#140e09] border border-[#3d291a] focus:border-[#e5a93c] rounded-lg px-3.5 py-2.5 text-sm text-[#faf5ea] placeholder-stone-600 focus:outline-none focus:ring-1 focus:ring-[#e5a93c] transition-colors"
                    >
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        id="btn-reset-submit"
                        class="w-full py-3 rounded-lg bg-[#b45309] hover:bg-[#d97706] text-[#faf5ea] font-poster text-lg tracking-wider uppercase shadow-xl transition-all duration-200 transform hover:-translate-y-0.5"
                    >
                        Gravar Nova Senha
                    </button>
                </div>

            </form>

            <div class="mt-6 pt-5 border-t border-[#3d291a] text-center text-xs text-stone-400">
                Lembrou sua senha antiga? 
                <a href="login.php" class="font-bold text-[#fde68a] hover:text-[#e5a93c] transition-colors ml-1">
                    Voltar para o Login →
                </a>
            </div>

        </div>

    </div>

</main>

<script>
function handleResetSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-reset-submit');
    const p1 = document.getElementById('new_password').value;
    const p2 = document.getElementById('confirm_password').value;

    if (p1 !== p2) {
        window.showToast('As senhas informadas não conferem.', 'warning');
        return;
    }

    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Atualizando senha...';

    const formData = new FormData(e.target);

    fetch('backend/actions/reset_password.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.showToast(data.message, 'success', 5000);
            setTimeout(() => {
                window.location.href = data.redirect || 'login.php';
            }, 1200);
        } else {
            btn.disabled = false;
            btn.textContent = originalText;
            window.showToast(data.message || 'Falha ao redefinir senha.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = originalText;
        window.showToast('Erro de conexão ao contatar o servidor.', 'error');
    });
}
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
