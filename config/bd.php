<?php
/**
 * Cine Palácio - Conexão Segura com Banco de Dados via PDO
 * Utiliza prepared statements para prevenir injeções SQL.
 */

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'cine_palacio');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

/**
 * Retorna a instância única do PDO (Singleton)
 * 
 * @return PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Tenta conectar ao servidor MySQL
            $tempPdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Garante que o banco de dados exista
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            unset($tempPdo);

            // Conecta ao banco de dados específico
            $dsnWithDb = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $pdo = new PDO($dsnWithDb, DB_USER, DB_PASS, $options);

            // Garante criação das tabelas essenciais se ainda não existirem
            ensureTablesExist($pdo);

        } catch (PDOException $e) {
            // Log do erro técnico e exibição amigável sem vazar credenciais
            error_log("Erro de Conexão com Banco Cine Palácio: " . $e->getMessage());
            die("<div style='background:#1c140d;color:#fde68a;padding:24px;font-family:sans-serif;max-width:600px;margin:50px auto;border-radius:12px;border:1px solid #b45309;box-shadow:0 10px 25px rgba(0,0,0,0.5);'>
                <h2 style='color:#e5a93c;margin-top:0;'>Cine Palácio - Conexão Indisponível</h2>
                <p>Não foi possível conectar ao banco de dados MySQL no momento.</p>
                <p style='color:#fca5a5;font-size:14px;background:#3b1111;padding:10px;border-radius:6px;'>" . htmlspecialchars($e->getMessage()) . "</p>
                <p style='font-size:13px;color:#d1d5db;'>Verifique se o serviço MySQL está ativo na porta " . htmlspecialchars(DB_PORT) . ".</p>
            </div>");
        }
    }

    return $pdo;
}

/**
 * Cria a estrutura inicial das tabelas se não existirem
 */
function ensureTablesExist(PDO $pdo): void {
    // Tabela de Usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `nome` VARCHAR(100) DEFAULT NULL,
            `reset_token` VARCHAR(100) DEFAULT NULL,
            `reset_expires_at` DATETIME DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_email (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Filmes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `filmes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `imdb_id` VARCHAR(50) UNIQUE DEFAULT NULL,
            `titulo` VARCHAR(255) NOT NULL,
            `titulo_original` VARCHAR(255) DEFAULT NULL,
            `ano` INT NOT NULL,
            `diretor` VARCHAR(255) DEFAULT NULL,
            `elenco` TEXT DEFAULT NULL,
            `generos` VARCHAR(255) NOT NULL,
            `classificacao_indicativa` VARCHAR(10) DEFAULT '14',
            `duracao_minutos` INT DEFAULT 110,
            `sinopse` TEXT NOT NULL,
            `capa_url` TEXT DEFAULT NULL,
            `imdb_rating` DECIMAL(3,1) DEFAULT 7.5,
            `imdb_votes` INT DEFAULT 5000,
            `is_cult` TINYINT(1) DEFAULT 0 COMMENT '0: Mais Conhecido, 1: Menos Conhecido / Cult / Lado B',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_filmes_titulo (`titulo`),
            INDEX idx_filmes_ano (`ano`),
            INDEX idx_filmes_cult (`is_cult`),
            INDEX idx_filmes_rating (`imdb_rating`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Avaliações (Estrelas de 1 a 5)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `filme_avaliacoes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `filme_id` INT NOT NULL,
            `estrelas` TINYINT NOT NULL COMMENT '1 a 5 estrelas',
            `comentario` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_user_filme_avaliacao (`user_id`, `filme_id`),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`filme_id`) REFERENCES `filmes`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Relação Usuário-Filme (Gostei e Assistido)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `user_filmes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `filme_id` INT NOT NULL,
            `assistido` TINYINT(1) DEFAULT 0,
            `gostei` TINYINT(1) DEFAULT 0,
            `assistido_em` DATETIME DEFAULT NULL,
            `gostei_em` DATETIME DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_user_filme_status (`user_id`, `filme_id`),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`filme_id`) REFERENCES `filmes`(`id`) ON DELETE CASCADE,
            INDEX idx_user_assistido (`user_id`, `assistido`),
            INDEX idx_user_gostei (`user_id`, `gostei`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Auditoria e Histórico (Requisito CORE: manter sempre os logs de qualquer alteração)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `auditoria_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT DEFAULT NULL,
            `user_email` VARCHAR(255) DEFAULT NULL,
            `acao` VARCHAR(100) NOT NULL,
            `detalhes` TEXT DEFAULT NULL,
            `ip_origem` VARCHAR(45) DEFAULT NULL,
            `user_agent` VARCHAR(255) DEFAULT NULL,
            `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_auditoria_user (`user_id`),
            INDEX idx_auditoria_acao (`acao`),
            INDEX idx_auditoria_data (`criado_em`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
