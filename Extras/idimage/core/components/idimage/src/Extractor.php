<?php

namespace IdImage;

use IdImage\Entities\EntityClose;
use IdImage\Helpers\Response;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Extractor
{

    public function extractionOffers(Response $Response, $offers)
    {
        $status_code = $Response->getStatus();
        $data = $Response->json();

        $items = [];
        if (!empty($data['errors']) && is_array($data['errors'])) {
            foreach ($data['errors'] as $k => $item) {
                list($k, $index, $field) = explode('.', $k);
                if (empty($items[$k])) {
                    $items[$k] = [];
                }
                $items[$k]['errors'] = $item;
            }
        }

        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $k => $item) {
                if (empty($items[$k])) {
                    $items[$k] = $item;
                } else {
                    $items[$k] = array_merge($items[$k], $item);
                }
            }
        }

        /* @var EntityClose $Offer */
        foreach ($items as $k => $item) {
            $Offer = $offers[$k];
            if (!empty($items[$k]['errors'])) {
                $Offer->setError($items[$k]['errors']);
            }
            $Offer->setStatusCode($status_code);
            $Offer->setReceived(true);
        }
    }

    public function pollItems(array $closes)
    {
        $items = [];

        if (count($closes) > 0) {
            foreach ($closes as $item) {
                $id = (int)$item['offer_id'];
                $closes = null;
                if (!empty($item['closes'])) {
                    foreach ($item['closes'] as $offer) {
                        $closes[$offer['offer_id']] = $offer['probability'];
                    }
                }
                $items[$id] = [
                    'status' => $item['status'],
                    'total_close' => $item['total_close'] ?? 0,
                    'min_scope' => $item['min_scope'] ?? 0,
                    'closes' => $closes,
                ];
            }
        }

        return $items;
    }

    public function extractCloses(string $closes_url)
    {
        $http = get_headers($closes_url)[0];
        if (strpos($http, '200') === false) {
            return null;
        }
        $content = file_get_contents($closes_url);
        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }
        if (empty($data['closes']) || !is_array($data['closes'])) {
            return null;
        }

        return $this->pollItems($data['closes']);
    }

}
