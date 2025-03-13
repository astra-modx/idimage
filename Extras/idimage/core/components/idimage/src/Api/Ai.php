<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use IdImage\Abstracts\ApiAbstract;
use IdImage\Interfaces\ApiInterfaces;

class Ai extends ApiAbstract implements ApiInterfaces
{
    public function embedding(string $imagePath, string $etag)
    {
        return $this->client->file('ai/embedding', $imagePath, [
            'etag' => $etag,
        ]);
    }

    public function embeddingUrl(string $pictureUrl, string $etag)
    {
        return $this->client->post('ai/embedding', [
            'etag' => $etag,
            'picture' => $pictureUrl,
        ]);
    }

    public function balance()
    {
        return $this->client->post('/ai/balance');
    }

}
