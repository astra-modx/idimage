<?php
include_once dirname(__FILE__) . '/update.class.php';
class idimageItemEnableProcessor extends idimageItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', true);
        return true;
    }
}
return 'idimageItemEnableProcessor';