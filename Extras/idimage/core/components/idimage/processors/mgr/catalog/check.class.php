<?php

class idImageCatalogCheckProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {
        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');


        if (!$idImage->hasToken()) {
            return $this->failure('Token is empty');
        }

        $Response = $idImage->actions()->info()->send();
        if (!$Response->isOk()) {
            return $this->failure($Response->getMsg());
        }
        $Catalog = $Response->entityCatalog();
        if ($Response->getStatus() !== 200 || !$Catalog->active()) {
            return $this->failure('Catalog is not active');
        }

        return $this->success('', [
            'status_code' => $Response->getStatus(),
        ]);
    }


}

return 'idImageCatalogCheckProcessor';
