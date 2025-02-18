<?php
class myappItemRemoveProcessor extends modObjectRemoveProcessor
{
    public $objectType = 'myappItem';
    public $classKey = 'myappItem';
    public $languageTopics = ['myapp:manager'];
    #public $permission = 'remove';

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

return 'myappItemRemoveProcessor';