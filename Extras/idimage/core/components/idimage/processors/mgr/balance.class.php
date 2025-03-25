<?php

class idImageBalanceProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $idImage->canToken();

        return $this->success('', [
            'balance' => $idImage->balance(),
        ]);
    }


}

return 'idImageBalanceProcessor';
