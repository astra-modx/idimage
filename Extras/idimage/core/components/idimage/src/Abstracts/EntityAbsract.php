<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


abstract class EntityAbsract
{

    public function isLocalUrl()
    {
        $picture = $this->getPicture();
        $parsedUrl = parse_url($picture);

        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return 'Not local address';
        }

        $host = strtolower($parsedUrl['host']);

        // Проверяем, является ли хост локальным
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return 'Invalid host, cannot use localhost or 127.0.0.1. Current url: '.PHP_EOL.$picture;
        }

        return true;
    }

    public function checkHttpStatus()
    {
        // Проверяем наличие файла на диске
        $picture = $this->getPicture();

        $headers = get_headers($picture, 1);

        if ($headers && strpos($headers[0], '200') !== false) {
            return true;
        } else {
            return "status checked ".$headers[0];
        }
    }
}
