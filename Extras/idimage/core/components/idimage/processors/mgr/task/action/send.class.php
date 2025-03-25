<?php

class idImageTaskSendProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    public function process()
    {
        $id = (int)$this->getProperty('id');
        /* @var idImageTask $Task */
        if (!$Task = $this->modx->getObject('idImageTask', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_get_task'));
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
