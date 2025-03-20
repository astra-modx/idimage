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

    public function criteria()
    {
        return [
            'upload' => false,
            'OR:task_id:=' => null,
        ];
    }
}

return 'idImageProductUploadProcessor';
