<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageCloseEnableProcessor extends idImageCloseUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);

        return true;
    }
}

return 'idImageCloseEnableProcessor';
