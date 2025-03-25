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
        if (empty($this->get('data'))) {
            return null;
        }
        $embedding = $this->get('data');
        if (empty($embedding)) {
            return null;
        }

        if (!is_array($embedding)) {
            return null;
        }

        if (count($embedding) != 512) {
            return null;
        }

        return $embedding;
    }
}
