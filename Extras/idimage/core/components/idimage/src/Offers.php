<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 18:00
 */

namespace IdImage;


use IdImage\Entities\EntityClose;

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
}
