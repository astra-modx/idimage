<?php

if (!class_exists('idImageProductTaskProcessor')) {
    include_once __DIR__.'/../task.class.php';
}

class idImageProductIndexedProcessor extends idImageProductTaskProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function operation(): string
    {
        return 'indexed';
    }
}

return 'idImageProductIndexedProcessor';
