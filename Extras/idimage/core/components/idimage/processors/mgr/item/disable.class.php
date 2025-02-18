<?php
include_once dirname(__FILE__) . '/update.class.php';
class idimageItemDisableProcessor extends idimageItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);
        return true;
    }
}
return 'idimageItemDisableProcessor';
