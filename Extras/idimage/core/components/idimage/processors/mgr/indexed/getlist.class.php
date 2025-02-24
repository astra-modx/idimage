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
        $array = $object->toArray();
        $IndexedAction = new \IdImage\Actions\IndexedAction($object, basename(__DIR__));
        $actions = $IndexedAction->getList(function ($action) {


            if ($action->get('version')) {
                // Использование текущей версии
                if ($action->get('completed') && $action->get('launch')) {
                    if (!$action->get('sealed')) {
                        $action->add('useVersion', 'icon-upload');
                    }
                }

                if (!$action->get('launch')) {
                    $action->add('launch', 'icon-play');
                }
            }

            $action->add('info', 'icon-info', false);
            /*if (!$action->get('active')) {
                $action->add('enable', 'icon-power-off action-green', false);
            } else {
                $action->add('disable', 'icon-power-off action-gray', false);
            }
*/
            $action->add('remove', 'icon icon-trash-o action-red', false);
        });
        $array['actions'] = $actions;

        return $array;
    }
}

return 'idImageIndexedGetListProcessor';
