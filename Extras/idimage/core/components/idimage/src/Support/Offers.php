<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 18:00
 */

namespace IdImage\Support;


use IdImage\Api\Entities\EntityClose;

class Offers
{

    /* @var EntityClose[] $items */
    private $items = [];

    public function addOffer(EntityClose $entity, int $statusInvalid)
    {
        $validate = $entity->validate();
        if ($validate !== true) {
            $entity->setStatus($statusInvalid);
            $entity->setError([
                $validate,
            ]);
        }

        $this->items[] = $entity;

        return $this;
    }

    public function get()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function isNotEmpty()
    {
        return !empty($this->items);
    }

    public function clear()
    {
        $this->items = [];

        return true;
    }

    public function extractor()
    {
        $items = null;

        if ($offers = $this->get()) {
            /* @var EntityClose $offer */
            foreach ($offers as $offer) {
                if (!$offer->isError()) {
                    $items[] = $offer->toArray();
                }
            }
        }

        return $items;
    }


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
