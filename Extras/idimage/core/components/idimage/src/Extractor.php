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
            $Offer->setReceived(true);
        }
    }


}
