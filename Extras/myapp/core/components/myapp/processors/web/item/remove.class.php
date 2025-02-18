<?php

class myappOfficeItemRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'myappItem';
    public $classKey = 'myappItem';
    public $languageTopics = ['myapp'];
    //public $permission = 'remove';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('myapp_item_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var myappItem $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('myapp_item_err_nf'));
            }

            $object->remove();
        }

        return $this->success();
    }

}

return 'myappOfficeItemRemoveProcessor';