<?php

class idImageMultipleProcessor extends modProcessor
{

    /**
     * @return array|string
     */
    public function process()
    {


        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->success();
        }

        /** @var idimage $idimage */
        $idimage = $this->modx->getService('idimage');
        $currentFolder = basename(__DIR__);
        foreach ($ids as $id) {
            /** @var modProcessorResponse $response */
            $response = $idimage->runProcessor('mgr/'.$currentFolder.'/'.$method, array('id' => $id));
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }


}

return 'idImageMultipleProcessor';
