<?php

abstract class idImageActionsProcessor extends modProcessor
{
    /* @var idImage $idImage */
    public $idImage;

    public function initialize()
    {
        $this->idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        return parent::initialize(); // TODO: Change the autogenerated stub
    }
}
