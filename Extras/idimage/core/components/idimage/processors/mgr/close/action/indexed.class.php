<?php

class idImageCloseActionIndexedProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
        }

        $task = $Close->taskIndexed();
        $task->send();

        return $this->success('success');
    }
}

return 'idImageCloseActionIndexedProcessor';
