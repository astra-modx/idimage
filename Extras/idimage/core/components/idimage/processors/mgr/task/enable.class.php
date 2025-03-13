<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageTaskEnableProcessor extends idImageTaskUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);

        return true;
    }
}

return 'idImageTaskEnableProcessor';
