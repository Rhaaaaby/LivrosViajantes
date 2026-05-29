<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    /**
     * Retorna a conexão PDO (singleton - cria apenas uma vez)
     * Usa valores do arquivo .env ou variáveis do servidor
     *
     * @return PDO
     * @throws PDOException 
     */
    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host     = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
        $port     = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432');
        $dbname   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'neondb');
        $user     = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'postgres');
        $password = getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: ($_ENV['DB_PASSWORD'] ?? ($_ENV['DB_PASS'] ?? '123456')));
        $appEnv   = getenv('APP_ENV')  ?: ($_ENV['APP_ENV']  ?? 'production');

        // Configuração da DSN limpa, sem repetições
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options='--client_encoding=UTF8'";

        try {
            self::$instance = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,          
                    PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]
            );

            return self::$instance;

        } catch (PDOException $e) {
            // Enviamos o erro real para o log da Render de qualquer forma
            error_log("Erro de conexão com banco: " . $e->getMessage());

            // Se der erro, vamos jogar o erro REAL na tela temporariamente para sabermos o motivo exato
            throw new PDOException("Erro Real: " . $e->getMessage());
        }
    }


    public static function reconnect(): PDO
    {
        self::$instance = null;
        return self::connect();
    }
}