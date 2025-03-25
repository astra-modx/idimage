<?php

class idImageTaskRemoveProcessor extends modObjectRemoveProcessor
{
    public $objectType = 'idImageTask';
    public $classKey = 'idImageTask';
    public $languageTopics = ['idimage:manager'];
    public $permission = 'remove';

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }
}

return 'idImageTaskRemoveProcessor';
