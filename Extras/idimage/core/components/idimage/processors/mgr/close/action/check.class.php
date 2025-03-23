<?php

class idImageCloseActionCheckProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
        }

        $Close->service()->runProcessor('mgr/actions/product/creation', [
            'ids' => [$Close->get('pid')],
        ]);

        return $this->success('success');
    }
}

return 'idImageCloseActionCheckProcessor';
