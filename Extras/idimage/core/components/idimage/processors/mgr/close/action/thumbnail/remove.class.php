<?php

class idImageTaskReceivedProcessor extends modProcessor
{
    public function process()
    {
        /** @var idimage $idimage */
        $idimage = $this->modx->getService('idimage');
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
        }

        $Close->removeThumbnail();

        return $this->success('success');
    }
}

return 'idImageTaskReceivedProcessor';
