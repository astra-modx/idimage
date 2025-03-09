<?php

namespace IdImage;

use idImage;
use IdImage\Api\Ai;
use IdImage\Support\Client;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Actions
{
    private Ai $ai;

    public function __construct(idImage $idImage)
    {
        $Client = new Client($idImage->modx);
        $this->ai = new Ai($Client);
    }

    public function ai()
    {
        return $this->ai;
    }
}
