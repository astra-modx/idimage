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
        $data = $Stat->toArray();
        $data = array_merge($data, [
            'tpl' => $Stat->tpl(),
        ]);

        return $this->success('', $data);
    }


}

return 'idImageStatProcessor';
