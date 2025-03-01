<?php

class idImageIndexedGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'idImageIndexed';
    public $classKey = 'idImageIndexed';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = ['idimage:manager'];
    //public $permission = 'list';


    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @param  xPDOQuery  $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim($this->getProperty('query'));
        if ($query) {
            $id = (int)$query;
            if ($id > 0) {
                $c->where([
                    'OR:id:=' => $id,
                    'OR:pid:=' => $id,
                ]);
            }
        }

        return $c;
    }


    /**
     * @param  xPDOObject  $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        /* @var idImageIndexed $object */
        $array = $object->toArray();
        // $Version = $object->version();


        $array['total_products'] = $this->modx->getCount('idImageClose');

        /*  $IndexedAction = new \IdImage\Support\IndexedAction($object, basename(__DIR__));
        $actions = $IndexedAction->getList(function (\IdImage\Support\IndexedAction $action) use ($object, $Version) {
             if ($Version->get('version')) {
                 if (!$action->get('sealed')) {
                     $exist = $Version->downloadLink();
                     if ($exist) {
                         $action->add('download', 'icon-download');
                     }
                 }
             }

             $action->add('poll', 'icon icon-refresh action-green');
         });
        $array['actions'] = $actions;*/

        return $array;
    }
}

return 'idImageIndexedGetListProcessor';
