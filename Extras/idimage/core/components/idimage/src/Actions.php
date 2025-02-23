<?php

namespace IdImage;

use Exception;
use modX;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Actions
{

    private Client $client;

    public function __construct(modX $modx)
    {
        $this->client = new Client($modx);
    }

    public function create(array $items)
    {
        return $this->client->post('images', ['items' => $items]);
    }

    public function delete(array $items)
    {
        return $this->client->delete('images', ['items' => $items]);
    }

    public function upload(string $offerId, string $imagePath)
    {
        return $this->client->upload($offerId, $imagePath);
    }

    public function poll(array $OfferIds)
    {
        return $this->client->get('images', ['offers' => $OfferIds]);
    }

    public function lastVersion()
    {
        return $this->client->get('images/last/version');
    }

    public function reindex()
    {
        return $this->client->post("images/service/reindex");
    }

    public function upVersion()
    {
        return $this->client->get("images/service/upVersion");
    }

    public function indexed()
    {
        return $this->client->get('indexed');
    }
    public function indexedLatest()
    {
        return $this->client->get('indexed/latest');
    }

    public function indexedCreate()
    {
        return $this->client->post('indexed');
    }


}
