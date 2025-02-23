<?php

if (!class_exists('idImageStatusProcessor')) {
    include_once __DIR__.'/default.class.php';
}

class idImageStatusQueue extends idImageStatusProcessor
{
    public function status()
    {
        return idImageClose::STATUS_QUEUE;
    }
}

return 'idImageStatusQueue';
