<?php

class idImageCloseActionSimilarRemoveProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    public function process()
    {
        $id = (int)$this->getProperty('id');

        /* @var idImageClose $Close */
        if (!$Close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
        }

        if ($similar = $Close->similar()) {
            $similar->remove();
        }

        return $this->success('success');
    }
}

return 'idImageCloseActionSimilarRemoveProcessor';
