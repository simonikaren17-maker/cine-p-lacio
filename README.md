# ★ CINE PALÁCIO ★
> Plataforma de Streaming & Catalogação do Cinema Brasileiro com Estética Setentista (inspirada em *O Agente Secreto* de 2025 e *Cidade de Deus*).

---

## 🎯 Objetivo
Um sistema web moderno e responsivo focado no **Cinema Brasileiro**, com filtragem entre filmes mais conhecidos e produções cult/lado B, busca dinâmica em tempo real com autocomplete, avaliação por estrelas (1 a 5), catalogação com classificação indicativa oficial, sinopses e capas, além de espaço pessoal para marcar filmes que gostou e assistiu.

---

## 🛠️ Stack Tecnológica
- **Backend**: PHP 8.3 estruturado de forma modular com sessões nativas (`session_start()`, cookies HttpOnly e SameSite=Lax).
- **Banco de Dados**: MySQL / MariaDB utilizando **PDO** com prepared statements estritos para proteção contra SQL Injection.
- **Frontend**: HTML5 semântico, Tailwind CSS (Mobile-First), CSS customizado com paleta vintage setentista (tons âmbar, mostarda, terracota e sépia).
- **Notificações & Diálogos**:
  - Sistema de **Toasts** flutuantes não-bloqueantes.
  - **Modais Customizados** substituindo todos os `alert()` e `confirm()` nativos do navegador.

---

## 📂 Arquitetura do Projeto
A estrutura do projeto foi dividida em camadas lógicas para evitar qualquer monolito e viabilizar futuras expansões:

```
unitivy/
├── config/
│   ├── bd.php                  # Conexão PDO com auto-criação do banco e tabelas
│   ├── auth.php                # Sessões nativas, criptografia Bcrypt e helpers
│   └── audit.php               # Logger central de auditoria com IP, data e detalhes
├── backend/
│   ├── actions/
│   │   ├── login.php           # Autenticação segura com verificação de hash
│   │   ├── register.php        # Cadastro simples com E-mail e Senha
│   │   ├── logout.php          # Encerramento de sessão
│   │   ├── reset_password.php  # Redefinição de senha com Bcrypt e auditoria
│   │   ├── poster_finder.php   # Busca inteligente de capas (Wikipédia/TMDb/SVG)
│   │   ├── seed_filmes.php     # 30+ clássicos do cinema brasileiro
│   │   └── import_imdb.php     # Importador referencial do IMDb (pt.imdb.com)
│   └── api/
│       ├── search.php          # API de busca dinâmica para autocomplete
│       ├── rate.php            # API para avaliação por estrelas (1 a 5)
│       ├── toggle_watched.php  # API para alternar status "Já assisti"
│       └── toggle_favorite.php # API para alternar status "Gostei" (Favoritos)
├── views/
│   ├── header.php              # Meta tags, Tailwind CSS e fontes anos 70
│   ├── navbar.php              # Header responsivo com busca dinâmica e menu mobile
│   ├── footer.php              # Rodapé temático com informações do sistema
│   ├── toast.php               # Container para notificações flutuantes
│   ├── modal.php               # Modal retrô de cinema substituto de alert/confirm
│   └── movie_card.php          # Card de filme reutilizável com capa, estrelas e badges
├── assets/
│   ├── css/
│   │   └── custom.css          # Paleta anos 70, tipografia retrô e selos oficiais
│   ├── js/
│   │   └── app.js              # Toasts, modais, busca ao digitar e chamadas AJAX
│   └── images/
│       └── retro_poster.php    # Gerador vetorial SVG de pôsteres setentistas
├── index.php                   # Catálogo principal com filtros e recomendações
├── filme.php                   # Ficha técnica completa do filme e sinopse
├── meu-espaco.php              # Área personalizada do usuário (Gostei / Assistidos)
├── historico.php               # Painel de Auditoria Geral e rastreamento de ações
├── init_db.php                 # Script de carga inicial do banco e acervo
└── iniciar.bat                 # Script de inicialização com 1 clique no Windows
```

---

## 🔒 Regras de Negócio e Segurança Implementadas

1. **Senhas com Hash Bcrypt**: Todas as senhas de usuários são tratadas e gravadas com `password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12])`.
2. **Página de Histórico e Auditoria Contínua**: Cada alteração no sistema (cadastros, logins, falhas de autenticação, redefinição de senha, avaliações, favoritos, marcação de assistidos, inclusões no acervo) é gravada na tabela `auditoria_logs` com IP de origem, agente do navegador, data/hora e descrição da operação. A página `historico.php` oferece filtros por ação e busca textual.
3. **Espaço Personalizado do Usuário**: Em `meu-espaco.php`, cada usuário registrado tem suas abas exclusivas para:
   - Filmes que gostou (Favoritos).
   - Filmes já assistidos (com data de conclusão).
   - Filmes avaliados com estrelas.
4. **Regra de Ocultação de Assistidos**: Na página inicial e nos filtros de catálogo, os filmes marcados como assistidos pelo usuário logado são ocultados por padrão da lista de novas sugestões.
5. **Busca Dinâmica com Autocomplete**: Ao digitar ao menos 2 caracteres na barra de pesquisa da navbar, uma consulta assíncrona filtra títulos, diretores, elenco e gêneros, exibindo um dropdown com miniatura, ano e classificação.
6. **Classificação por Estrelas (1 a 5)**: Usuários avaliam os filmes clicando diretamente nas 5 estrelas do card ou da página de detalhes, calculando a média da comunidade e atualizando a interface sem recarregar a tela.
7. **Localizador Inteligente de Capas**: Caso um filme não possua capa no IMDb ou o link falhe, o módulo `poster_finder.php` consulta a Wikipédia em português pelo título e diretor; se ainda assim não houver imagem, um cartaz vetorial retrô em SVG com textura de celuloide dos anos 70 é renderizado dinamicamente.
8. **Classificação Indicativa Oficial**: Exibição dos selos nas cores oficiais brasileiras (**L**, **10**, **12**, **14**, **16**, **18** anos).

---

## 🚀 Como Executar

### Opção 1: Execução com 1 Clique (Windows)
Dê um duplo clique no arquivo:
```cmd
iniciar.bat
```
O script iniciará o MariaDB (porta 3306), rodará o `init_db.php` e iniciará o servidor PHP em `http://localhost:8000`, abrindo seu navegador automaticamente.

### Opção 2: Linha de Comando Manual
1. Inicie o servidor MariaDB/MySQL na porta 3306.
2. No terminal da pasta do projeto, inicialize o banco:
   ```cmd
   php init_db.php
   ```
3. Inicie o servidor PHP embutido:
   ```cmd
   php -S 127.0.0.1:8000
   ```
4. Acesse no navegador:
   ```
   http://localhost:8000
   ```

---

## 👤 Credenciais de Demonstração
- **E-mail**: `cinefilo@cinepalacio.br`
- **Senha**: `cinema123`
*(Você também pode criar novos usuários na página de Cadastro com qualquer e-mail e senha).*
