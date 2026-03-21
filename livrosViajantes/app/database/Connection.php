<?php

class Connection
{
    private static ?PDO $instance = null;

    /**
     * Retorna a conexão PDO (singleton - cria apenas uma vez)
     * Usa valores do arquivo .env
     *
     * @return PDO
     * @throws PDOException Se a conexão falhar
     */
    
    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Lê do .env (com valores padrão de fallback para desenvolvimento local)
        $host     = $_ENV['DB_HOST']     ?? 'localhost';
        $port     = $_ENV['DB_PORT']     ?? '5432';
        $dbname   = $_ENV['DB_NAME']     ?? 'livros_viajantes';
        $user     = $_ENV['DB_USER']     ?? 'postgres';
        $password = $_ENV['DB_PASS']     ?? '';

        // Monta a string DSN para PostgreSQL
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};";

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
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                throw $e; // mostra erro completo só em dev
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