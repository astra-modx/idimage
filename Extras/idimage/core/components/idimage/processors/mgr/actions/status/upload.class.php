<?php

if (!class_exists('idImageStatusProcessor')) {
    include_once __DIR__.'/default.class.php';
}

class idImageStatusUpload extends idImageStatusProcessor
{
    public function status()
    {
        return idImageClose::STATUS_UPLOAD;
    }
}

return 'idImageStatusUpload';
