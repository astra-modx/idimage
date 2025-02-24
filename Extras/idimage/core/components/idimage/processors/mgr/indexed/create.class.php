<?php

class idImageIndexedCreateProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    /**
     * @return bool
     */
    public function process()
    {
        $this->setProperty('mode', 'new');

        // Здесь создаем новый индекс

        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $Response = $idImage->actions()->indexedCreate()->send();
        if ($Response->getStatus() !== 201) {
            return $this->failure('Ошибка при создании индекса', [
                'status' => $Response->getStatus(),
            ]);
        }

        /* @var idImageIndexed $Indexed */
        $Indexed = $this->modx->newObject('idImageIndexed');
        $Entity = $Indexed->entity()->fromArray($Response->json());

        $Indexed->fromArray($Entity->toArray());
        $Indexed->save();

        if ($Indexed->save()) {
            $Indexed->deactivate();
        }

        return $this->success('', $Indexed->toArray());
    }

}

return 'idImageIndexedCreateProcessor';
