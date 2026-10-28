@echo off
chcp 65001 > nul
title Cine Palácio - Servidor Local

echo =======================================================
echo              ★ CINE PALÁCIO ★
echo    Cinema Brasileiro • Estética 1970s
echo =======================================================
echo.

:: Verifica se o MySQL/MariaDB está rodando
netstat -ano | findstr ":3306" > nul
if %ERRORLEVEL% neq 0 (
    echo [1/3] Iniciando servidor de Banco de Dados MariaDB...
    start "" /B "C:\Users\Aluno\tools\mariadb\bin\mysqld.exe" --datadir="C:\Users\Aluno\tools\mariadb\data" --port=3306
    timeout /t 3 /nobreak > nul
) else (
    echo [1/3] Banco de Dados MariaDB ativo na porta 3306.
)

:: Inicializa o Banco e o Acervo se necessário
echo [2/3] Verificando acervo de filmes nacionais...
"C:\Users\Aluno\tools\php\php.exe" "init_db.php" > nul

:: Abre o navegador
echo [3/3] Abrindo o Cine Palácio no seu navegador...
start http://localhost:8000

echo.
echo =======================================================
echo  Servidor Ativo em: http://localhost:8000
echo  Pressione Ctrl+C nesta janela para encerrar.
echo =======================================================
echo.

"C:\Users\Aluno\tools\php\php.exe" -S 127.0.0.1:8000 -t "%~dp0"
pause
