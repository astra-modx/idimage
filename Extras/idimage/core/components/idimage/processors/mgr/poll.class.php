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

        $Indexed = $idImage->indexed();

        $Entity = $Indexed->api()->entity();


        /* @var idImageIndexed $Indexed */
        if (!$Indexed = $this->modx->getObject('idImageIndexed', ['code' => $Entity->code()])) {
            $Indexed = $this->modx->newObject('idImageIndexed');
            $Indexed->set('code', $Entity->code());
        }

        $Indexed->set('name', $Entity->name());
        $Indexed->set('active', $Entity->active());
        $Indexed->set('upload_api', $Entity->uploadApi());


        if (!$Indexed->save()) {
            return $this->failure('Unable to save version');
        }

        if ($Entity->version() > 0) {
            $Version = $Indexed->version();

            // Если версия поднялась то создаем новую версию
            if ($Version->get('version') < $Entity->version()) {
                $Version = $this->modx->newObject('idImageVersion');
                $Version->set('indexed_id', $Indexed->get('id'));
                $Indexed->addOne($Version);
            }

            $Version->fromArray($Entity->toArray());

            if (!$Version->save()) {
                return $this->failure('Unable to save version');
            }
        }

        return $this->success('', $Entity->toArray());
    }


}

return 'idImageStatProcessor';
