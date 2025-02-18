<?php
include_once dirname(__FILE__) . '/update.class.php';
class myappItemEnableProcessor extends myappItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);
        return true;
    }
}
return 'myappItemEnableProcessor';