<?php

class idImageTaskEmbeddingProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_get_task'));
        }

        if (!$Close->taskId()) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_task'));
        }

        $task = $Close->taskEmbedding();
        $task->send();

        return $this->success('success');
    }
}

return 'idImageTaskEmbeddingProcessor';
