<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageIndexedEnableProcessor extends idImageIndexedUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);

        return true;
    }
}

return 'idImageIndexedEnableProcessor';
