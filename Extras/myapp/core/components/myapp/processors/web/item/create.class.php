<?php

class myappOfficeItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'myappItem';
    public $classKey = 'myappItem';
    public $languageTopics = ['myapp'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('myapp_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('myapp_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'myappOfficeItemCreateProcessor';