<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use IdImage\Abstracts\ApiAbstract;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Interfaces\ApiInterfaces;

class Ai extends ApiAbstract implements ApiInterfaces
{
    public function embedding(string $imagePath)
    {
        $size = @getimagesize($imagePath);
        if ($size[0] !== 224 || $size[1] !== 224) {
            throw new ExceptionJsonModx('Неверный размер изображения, должно быть 224х224');
        }

        if ($size['mime'] !== 'image/jpeg') {
            throw new ExceptionJsonModx('Неверный формат изображения, должно быть jpeg');
        }

        return $this->client->embedding('ai/embedding', $imagePath);
    }

    public function embeddingUrl(string $url)
    {
        return $this->client->embeddingUrl('ai/embedding', $url);
    }

    public function balance()
    {
        return $this->client->post('/ai/balance');
    }

}
