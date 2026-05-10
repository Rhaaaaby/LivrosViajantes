<?php

namespace App\Utils;

class Response
{
    // Resposta de sucesso
    public static function success(string $message, int $statusCode = 200, $data = null): array
    {
        $response = [
            'sucesso' => true,
            'mensagem' => $message
        ];

        if ($data !== null) {
            $response['dados'] = $data;
        }

        http_response_code($statusCode);
        return $response;
    }

    // Resposta de erro
    public static function error(string $message, int $statusCode = 400): array
    {
        http_response_code($statusCode);
        return [
            'sucesso' => false,
            'erro' => $message
        ];
    }
}