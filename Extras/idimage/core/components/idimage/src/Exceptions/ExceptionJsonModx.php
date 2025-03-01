<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 01.11.2023
 * Time: 10:45
 */

namespace IdImage\Exceptions;

use Exception;
use Throwable;

class ExceptionJsonModx extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // Если запрос требует JSON, то отправляем JSON с ошибкой через MODX
        global $modx;

        // Отправляем JSON через MODX
        @header('Content-Type: application/json');
        $response = $modx->error->failure($message);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit; // Завершаем выполнение скрипта после отправки JSON
    }
}
