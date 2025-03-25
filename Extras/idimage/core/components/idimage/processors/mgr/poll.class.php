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


        try {
            $balance = $idImage->api()->ai()->balance();
        } catch (Exception $e) {
            $balance = 0;
        }


        $Stat = new \IdImage\Stat($idImage);
        $Stat->process();

        $data = $Stat->toArray();
        $data = array_merge($data, [
            'status' => 'ok',
            'balance' => $balance,
        ]);

        return $this->success('', $data);
    }


}

return 'idImageStatProcessor';
