<?php

if (!class_exists('idImageActionsStatusProcessor')) {
    include_once __DIR__.'/actions.class.php';
}

class idImageActionsStatusProcessing extends idImageActionsStatusProcessor
{
    public function status()
    {
        dd('processing');

        return idImageClose::STATUS_PROCESSING;
    }

}

return 'idImageActionsStatusProcessing';
