<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageTaskResetAttemptsProcessor extends idImageTaskUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('attempt', 0);
        $this->setProperty('status', idImageTask::STATUS_PENDING);

        return true;
    }
}

return 'idImageTaskResetAttemptsProcessor';
