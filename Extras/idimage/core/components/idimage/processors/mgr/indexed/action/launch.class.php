<?php

class idImageIndexedActionLaunchProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        $id = $this->getProperty('id');


        /* @var idImageIndexed $Indexed */
        if (!$Indexed = $this->modx->getObject('idImageIndexed', $id)) {
            return $this->failure('Индекс не найден');
        }

        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $Response = $idImage->actions()->indexedLaunch($Indexed->get('version'))->send();
        if ($Response->isFail()) {
            return $this->failure($Response->getMsg());
        }

        $Indexed->fromArray($Response->json());
        $Indexed->save();

        return $this->success('', $Indexed->toArray());
    }

}

return 'idImageIndexedActionLaunchProcessor';
