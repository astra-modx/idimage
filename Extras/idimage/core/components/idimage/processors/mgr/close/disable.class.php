<?php

include_once dirname(__FILE__).'/update.class.php';

class idImageCloseDisableProcessor extends idImageCloseUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);

        return true;
    }
}

return 'idImageCloseDisableProcessor';
