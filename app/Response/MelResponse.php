<?php


namespace App\Response;


trait MelResponse
{
    public static function success($text, $data)
    {
        $statusCode = 200;

        return \response([
            'type' => 'success',
            'text' => $text ? $text : "Requisição bem sucedida!",
            'data' => $data ? $data : [],
        ], $statusCode);
    }

    public static function warning($text, $data)
    {
        $statusCode = 202;

        return \response([
            'type' => 'warning',
            'text' => $text ? $text : "Atenção!",
            'data' => $data ? $data : []
        ], $statusCode);
    }

    public static function error($text)
    {
        $statusCode = 500;

        return \response([
            'type' => 'error',
            'text' => $text ? $text : "Erro ao executar requisição!",
        ], $statusCode);
    }

    public static function validationError(Array $errors) {

        foreach ($errors as $errorKey => $errorValue) {
            foreach ($errorValue as $errorText) {
                $errorMessage = $errorText;
            }
        }

        $statusCode = 500;

        return \response([
            'type' => 'error',
            'text' => $errorMessage,
        ], $statusCode);
    }

}
