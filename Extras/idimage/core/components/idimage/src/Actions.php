<?php

namespace IdImage;

use idImage;
use IdImage\Api\Queue;
use IdImage\Api\Indexed;
use IdImage\Support\Client;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Actions
{

    protected Queue $queue;
    protected Indexed $indexed;

    public function __construct(idImage $idImage)
    {
        $Client = new Client($idImage->modx);
        $this->queue = new Queue($Client);
        $this->indexed = new Indexed($Client);
    }

    public function queue()
    {
        return $this->queue;
    }

    public function indexed()
    {
        return $this->indexed;
    }

}
