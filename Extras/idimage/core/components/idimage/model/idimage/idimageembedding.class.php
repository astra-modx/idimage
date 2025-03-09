<?php

class idImageEmbedding extends xPDOSimpleObject
{

    public function save($cacheFlag = null)
    {
        if (!$this->isNew()) {
            $this->set('updatedon', time());
        } else {
            $this->set('createdon', time());
        }

        return parent::save($cacheFlag);
    }

    public function getEmbedding()
    {
        if ($this->get('embedding') == 0) {
            return null;
        }
        $embedding = $this->get('embedding');
        if (empty($embedding)) {
            return null;
        }

        return $embedding;
    }
}
