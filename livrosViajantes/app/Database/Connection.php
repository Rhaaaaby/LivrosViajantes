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

        // 1. Mudado de DB_PASS para DB_PASSWORD para bater com o padrão da Render/Neon
        $host     = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
        $port     = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432');
        $dbname   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'livros_viajantes');
        $user     = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'postgres');
        $password = getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: ($_ENV['DB_PASSWORD'] ?? ($_ENV['DB_PASS'] ?? '123456')));
        $appEnv   = getenv('APP_ENV')  ?: ($_ENV['APP_ENV']  ?? 'production');

        // 2. String de conexão limpa e direta com o encoding correto aceito pelo PDO pgsql
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options='--client_encoding=UTF8'";

        // Mantém a opção de enconding UTF8 que você já tinha
        $dsn .= "options='--client_encoding=UTF8'";

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
            if ($appEnv === 'production') {
                throw $e;
            }

            error_log("Erro de conexão com banco: " . $e->getMessage());
            throw new PDOException("Não foi possível conectar ao banco de dados. Tente novamente mais tarde.");
        }
    }

    public static function reconnect(): PDO
    {
        self::$instance = null;
        return self::connect();
    }
}