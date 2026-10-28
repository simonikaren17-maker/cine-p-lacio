<?php
/**
 * Cine Palácio - Módulo de Auditoria e Logs
 * Registra histórico de todas as alterações feitas no sistema para fins de auditoria.
 */

require_once __DIR__ . '/bd.php';

/**
 * Registra uma entrada na tabela de auditoria
 * 
 * @param PDO $pdo Instância ativa do PDO
 * @param int|null $userId ID do usuário (ou null para ações anônimas)
 * @param string|null $userEmail E-mail do usuário
 * @param string $acao Nome da ação (ex: 'CADASTRO', 'LOGIN', 'AVALIAR_FILME', etc.)
 * @param string|null $detalhes Descrição ou detalhes técnicos/operacionais da ação
 * @return bool Sucesso da gravação do log
 */
function logAudit(PDO $pdo, ?int $userId, ?string $userEmail, string $acao, ?string $detalhes = null): bool {
    try {
        // Obter IP do cliente de forma confiável
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (strpos($ip, ',') !== false) {
            $parts = explode(',', $ip);
            $ip = trim($parts[0]);
        }
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido', 0, 250);

        $stmt = $pdo->prepare("
            INSERT INTO auditoria_logs (user_id, user_email, acao, detalhes, ip_origem, user_agent, criado_em)
            VALUES (:user_id, :user_email, :acao, :detalhes, :ip_origem, :user_agent, NOW())
        ");

        return $stmt->execute([
            ':user_id'    => $userId,
            ':user_email' => $userEmail,
            ':acao'       => strtoupper(trim($acao)),
            ':detalhes'   => $detalhes,
            ':ip_origem'  => $ip,
            ':user_agent' => $userAgent
        ]);
    } catch (PDOException $e) {
        // Falha no log de auditoria não deve derrubar o sistema, mas deve ser registrada
        error_log("Falha ao registrar auditoria no Cine Palácio: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca os logs de auditoria com filtros opcionais
 * 
 * @param PDO $pdo
 * @param array $filtros ['acao' => string, 'busca' => string, 'user_id' => int, 'limite' => int, 'offset' => int]
 * @return array
 */
function getAuditLogs(PDO $pdo, array $filtros = []): array {
    $where = [];
    $params = [];

    if (!empty($filtros['acao'])) {
        $where[] = "acao = :acao";
        $params[':acao'] = $filtros['acao'];
    }

    if (!empty($filtros['user_id'])) {
        $where[] = "user_id = :user_id";
        $params[':user_id'] = (int)$filtros['user_id'];
    }

    if (!empty($filtros['busca'])) {
        $where[] = "(detalhes LIKE :busca OR user_email LIKE :busca OR ip_origem LIKE :busca)";
        $params[':busca'] = '%' . $filtros['busca'] . '%';
    }

    $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $limit = isset($filtros['limite']) ? (int)$filtros['limite'] : 50;
    $offset = isset($filtros['offset']) ? (int)$filtros['offset'] : 0;

    $sql = "
        SELECT id, user_id, user_email, acao, detalhes, ip_origem, user_agent, criado_em
        FROM auditoria_logs
        {$whereSql}
        ORDER BY id DESC
        LIMIT {$limit} OFFSET {$offset}
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Retorna a contagem total de logs para paginação
 */
function getAuditLogsCount(PDO $pdo, array $filtros = []): int {
    $where = [];
    $params = [];

    if (!empty($filtros['acao'])) {
        $where[] = "acao = :acao";
        $params[':acao'] = $filtros['acao'];
    }

    if (!empty($filtros['user_id'])) {
        $where[] = "user_id = :user_id";
        $params[':user_id'] = (int)$filtros['user_id'];
    }

    if (!empty($filtros['busca'])) {
        $where[] = "(detalhes LIKE :busca OR user_email LIKE :busca OR ip_origem LIKE :busca)";
        $params[':busca'] = '%' . $filtros['busca'] . '%';
    }

    $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $sql = "SELECT COUNT(*) FROM auditoria_logs {$whereSql}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}
