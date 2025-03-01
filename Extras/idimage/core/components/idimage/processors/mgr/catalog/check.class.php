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

        $Indexed = $idImage->api()->indexed()->entity();
        if (!$Indexed->active()) {
            return $this->failure('Catalog is not active');
        }

        return $this->success('');
    }


}

return 'idImageCatalogCheckProcessor';
