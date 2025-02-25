<?php

use IdImage\Entities\EntityIndexed;

class idImageIndexedActionUseVersionProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $id = $this->getProperty('id');

        /* @var idImageIndexed $Indexed */
        if (!$Indexed = $this->modx->getObject('idImageIndexed', $id)) {
            return $this->failure('Индекс не найден');
        }

        $Entity = $Indexed->entity()->fromArray($Indexed->toArray());


        $target = $Indexed->versionZip();
        $Reader = $Entity->readerIndexed();
        try {
            $Reader->download($Entity->downloadLink(), $Indexed->versionZip());
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }

        if (!file_exists($target)) {
            throw new Exception("Error download {$target}");
        }

        if (!$Indexed->versionJsonExists()) {
            throw new Exception("Error json {$Indexed->versionJson()}");
        }

        return $this->success('');
    }

}

return 'idImageIndexedActionUseVersionProcessor';
