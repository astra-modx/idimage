<?php

if (!class_exists('idImageProductTaskProcessor')) {
    include_once __DIR__.'/../task.class.php';
}

class idImageProductUploadProcessor extends idImageProductTaskProcessor implements \IdImage\Interfaces\ActionProgressBar
{
    public function operation(): string
    {
        return 'upload';
    }
}

return 'idImageProductUploadProcessor';
