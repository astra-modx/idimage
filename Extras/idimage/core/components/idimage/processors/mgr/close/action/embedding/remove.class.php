<?php

class idImageCloseActionEmbeddingRemoveProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    public function process()
    {
        $id = (int)$this->getProperty('id');


        /* @var idImageClose $close */
        if (!$close = $this->modx->getObject('idImageClose', $id)) {
            return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
        }

        /* @var idImageClose $Close */
        if ($Embedding = $this->modx->getObject('idImageEmbedding', ['hash' => $close->get('hash')])) {
            $Embedding->remove();
        }

        return $this->success('success');
    }
}

return 'idImageCloseActionEmbeddingRemoveProcessor';
