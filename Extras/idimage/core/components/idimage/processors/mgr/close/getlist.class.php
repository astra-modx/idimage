<?php

class idImageCloseGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'idImageClose';
    public $classKey = 'idImageClose';
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
            $c->where([
                'picture:LIKE' => "%{$query}%",
                'OR:hash:LIKE' => "%{$query}%",
            ]);
            $id = (int)$query;
            if ($id > 0) {
                $c->where([
                    'OR:id:=' => $id,
                    'OR:pid:=' => $id,
                ]);
            }
        }

        $received = $this->getProperty('received');
        if ($received != '') {
            $c->where("{$this->objectType}.received={$received}");
        }

        $pid = trim($this->getProperty('pid'));
        if (!empty($pid)) {
            $c->where("{$this->objectType}.pid={$pid}");
        }

        $status = trim($this->getProperty('status'));
        if (!empty($status)) {
            $c->where("{$this->objectType}.status={$status}");
        }

        $status_service = trim($this->getProperty('status_service'));
        if (!empty($status_service)) {
            $c->where("{$this->objectType}.status_service={$status_service}");
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
        $cloud = $this->setCheckbox('cloud');

        $IndexedAction = new \IdImage\Support\IndexedAction($object, basename(__DIR__));
        $actions = $IndexedAction->getList(function ($action) use ($cloud) {
            if (!$cloud) {
                //$action->add('update', 'icon-edit');

                /*if (!$action->get('active')) {
                    $action->add('enable', 'icon-power-off action-green');
                } else {
                    $action->add('disable', 'icon-power-off action-gray');
                }

                $action->add('remove', 'icon icon-trash-o action-red');*/
            } else {
                $action->add('upload', 'icon icon-upload');
            }
        });
        $array['actions'] = $actions;

        return $array;
    }
}

return 'idImageCloseGetListProcessor';
