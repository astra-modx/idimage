<?php

if (!class_exists('idImageActionsStatusProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageActionsImageStatusQueue extends idImageActionsStatusProcessor
{
    public function status()
    {
        return idImageClose::STATUS_QUEUE;
    }
}

return 'idImageActionsImageStatusQueue';
