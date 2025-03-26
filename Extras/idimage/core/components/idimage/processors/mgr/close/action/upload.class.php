<?php

class idImageCloseActionUploadProcessor extends modProcessor
{
    public function process()
    {
        /** @var idimage $idimage */
        $idimage = $this->modx->getService('idimage');
        $id = (int)$this->getProperty('id');
        $product_id = (int)$this->getProperty('product_id');


        /* @var idImageClose $Close */
        $Close = $idimage->query()->getCloseOrCreate($id, $product_id);
        if (is_string($Close)) {
            return $this->failure($Close);
        }

        $task = $Close->taskUpload();
        $task->send();

        return $this->success('success', [
            'close_id' => $Close->get('id'),
        ]);
    }
}

return 'idImageCloseActionUploadProcessor';
