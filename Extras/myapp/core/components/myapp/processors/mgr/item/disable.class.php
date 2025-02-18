<?php
include_once dirname(__FILE__) . '/update.class.php';
class myappItemDisableProcessor extends myappItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);
        return true;
    }
}
return 'myappItemDisableProcessor';
