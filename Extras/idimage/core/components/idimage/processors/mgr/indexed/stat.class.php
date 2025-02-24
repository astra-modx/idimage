<?php

class idImageIndexedStatProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


        return $this->success('', [
            'images' => $idImage->query()->closes()->count(),
            'closes' => $idImage->query()->closes()->where(['status' => idImageClose::STATUS_PROCESSING])->count(),
        ]);
    }


}

return 'idImageIndexedStatProcessor';
