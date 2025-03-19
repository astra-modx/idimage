<?php

class idImageTaskSendProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        /* @var idImageTask $Task */
        if (!$Task = $this->modx->getObject('idImageTask', $id)) {
            throw new \IdImage\Exceptions\ExceptionJsonModx('Не удалось получить Task для id');
        }

        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $response = $idImage->runProcessor('mgr/actions/task/send', [
            'ids' => [$Task->get('id')],
        ]);

        return $response->response;
    }
}

return 'idImageTaskSendProcessor';
