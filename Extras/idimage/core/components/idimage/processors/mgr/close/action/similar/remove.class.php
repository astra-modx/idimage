<?php

class idImageCloseActionSimilarRemoveProcessor extends modProcessor
{
    public $languageTopics = ['idimage:manager'];

    public function process()
    {
        /** @var idimage $idimage */
        $idimage = $this->modx->getService('idimage');
        $id = (int)$this->getProperty('id');
        $criteria = [
            'id' => $id,
        ];
        $product_id = (int)$this->getProperty('product_id');
        if (!empty($product_id)) {
            $criteria = [
                'pid' => $product_id,
            ];
            if (!$Similar = $this->modx->getObject('idImageSimilar', $criteria)) {
                return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
            } else {
                $Similar->remove();
            }
        } else {
            /* @var idImageClose $Close */
            if (!$Close = $this->modx->getObject('idImageClose', $criteria)) {
                return $this->failure($this->modx->lexicon('idimage_error_close_not_found'));
            }
            if ($similar = $Close->similar()) {
                $similar->remove();
            }
        }


        return $this->success('success');
    }
}

return 'idImageCloseActionSimilarRemoveProcessor';
