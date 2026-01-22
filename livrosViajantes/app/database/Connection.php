<?php

class Connection
{
    public static function connect()
    {
        return new PDO(
            "pgsql:host=localhost;port=5432;dbname=livros_viajantes",
            "postgres",
            "123456",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}