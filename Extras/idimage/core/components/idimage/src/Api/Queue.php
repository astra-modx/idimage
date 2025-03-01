<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use IdImage\Abstracts\ApiAbstract;
use IdImage\Api\Entities\EntityCatalog;
use IdImage\Api\Entities\EntityQueue;
use IdImage\Exceptions\ExceptionJsonModx;
use IdImage\Interfaces\ApiInterfaces;
use IdImage\Support\Offers;

class Queue extends ApiAbstract implements ApiInterfaces
{

    public function entity(): EntityCatalog
    {
        return $this->_entity(EntityQueue::class);
    }

    public function add(Offers $Offers, $callback = null)
    {
        return $this->queue('add', $Offers, $callback);
    }

    public function delete(Offers $Offers, $callback = null)
    {
        return $this->queue('delete', $Offers, $callback);
    }

    private function queue(string $action, Offers $Offers, $callback = null)
    {
        if ($Offers->isNotEmpty()) {
            $offersItems = $Offers->get();

            // Извлекаем offer для отправки
            if ($items = $Offers->extractor()) {
                $response = null;
                if ($action === 'add') {
                    $response = $this->post('images', ['items' => $items])->send();
                } elseif ($action === 'delete') {
                    $response = $this->del('images', ['items' => $items])->send();
                }

                if ($response) {
                    $Offers->extractionOffers($response, $offersItems);
                }
            }


            if (is_callable($callback)) {
                foreach ($offersItems as $offer) {
                    $callback($offer);
                }
            }
        }

        return true;
    }


    public function upload(string $offerId, string $imagePath)
    {
        $size = @getimagesize($imagePath);

        if ($size[0] !== 224 || $size[1] !== 224) {
            throw new ExceptionJsonModx('Неверный размер изображения, должно быть 224х224');
        }

        if ($size['mime'] !== 'image/jpeg') {
            throw new ExceptionJsonModx('Неверный формат изображения, должно быть jpeg');
        }

        return $this->client->upload('images/service/upload', $offerId, $imagePath);
    }

}
