<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageIndexedDisableProcessor extends idImageIndexedUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);

        return true;
    }
}

return 'idImageIndexedDisableProcessor';
