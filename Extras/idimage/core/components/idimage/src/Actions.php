<?php

namespace IdImage;

use idImage;
use IdImage\Api\Ai;
use IdImage\Api\Task;
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
    private Task $task;

    public function __construct(idImage $idImage)
    {
        $Client = new Client($idImage->modx);
        $this->ai = new Ai($Client);
        $this->task = new Task($Client);
    }

    public function ai()
    {
        return $this->ai;
    }

    public function task()
    {
        return $this->task;
    }
}
