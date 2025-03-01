<?php

class idImageStatProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


        $Stat = new \IdImage\Stat($idImage);

        $Stat->process();

        return $this->success('', [
            'tpl' => $Stat->tpl(),
            'stat' => $Stat->toArray(),
        ]);
    }


}

return 'idImageStatProcessor';
