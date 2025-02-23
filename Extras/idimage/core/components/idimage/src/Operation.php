<?php

namespace IdImage;

use Exception;
use idImage;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Operation
{
    /* @var idImage $idImage */
    private $idImage;

    /**
     * @var string
     */
    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
    }


    private function queue(string $action, Offers $Offers, $callback = null)
    {
        if ($Offers->isNotEmpty()) {
            // Извлекаем offer для отправки
            if ($items = $Offers->extractor()) {
                $response = null;
                if ($action === 'add') {
                    $response = $this->idImage->actions()->create($items)->send();
                } elseif ($action === 'delete') {
                    $response = $this->idImage->actions()->delete($items)->send();
                }

                if ($response) {
                    $offers = $Offers->get();
                    $this->idImage->extractor()->extractionOffers($response, $offers);
                    if (is_callable($callback)) {
                        foreach ($offers as $offer) {
                            $callback($offer);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function addQueue(Offers $Offers, $callback = null)
    {
        return $this->queue('add', $Offers, $callback);
    }

    public function deleteQueue(Offers $Offers, $callback = null)
    {
        return $this->queue('delete', $Offers, $callback);
    }

    public function upload(string $offerId, string $imagePath)
    {
        $size = @getimagesize($imagePath);

        if ($size[0] !== 224 || $size[1] !== 224) {
            throw new Exception('Неверный размер изображения, должно быть 224х224');
        }

        if ($size['mime'] !== 'image/jpeg') {
            throw new Exception('Неверный формат изображения, должно быть jpeg');
        }

        return $this->idImage->actions()->upload($offerId, $imagePath)->send();
    }

    public function lastVersion()
    {
        $Response = $this->idImage->actions()->lastVersion()->send();
        if (!$Response->isOk()) {
            throw $Response->exception();
        }

        return $Response;
    }

    public function indexed()
    {

        $Response = $this->idImage->actions()->indexed()->send();
        if (!$Response->isOk()) {
            throw $Response->exception();
        }

        return $Response;
    }


}
