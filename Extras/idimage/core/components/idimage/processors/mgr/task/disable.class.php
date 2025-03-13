<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageTaskDisableProcessor extends idImageTaskUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);

        return true;
    }
}

return 'idImageTaskDisableProcessor';
