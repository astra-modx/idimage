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
        $c->select($this->modx->getSelectColumns('idImageClose', 'idImageClose'));

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where([
                'idImageClose.picture:LIKE' => "%{$query}%",
                'OR:idImageClose.hash:LIKE' => "%{$query}%",
                'OR:msProduct.pagetitle:LIKE' => "%{$query}%",
            ]);
            $id = (int)$query;
            if ($id > 0) {
                $c->where([
                    'OR:idImageClose.id:=' => $id,
                    'OR:idImageClose.pid:=' => $id,
                ]);
            }
        }


        $received = $this->getProperty('received');
        if ($received != '') {
            $c->where("{$this->objectType}.received={$received}");
        }

        $similar = $this->getProperty('similar');
        if ($similar != '') {
            if ($similar == '1') {
                $c->where("{$this->objectType}.total!=0");
            } else {
                $c->where("{$this->objectType}.total=0");
            }
        }

        $pid = trim($this->getProperty('pid'));
        if (!empty($pid)) {
            $c->where("{$this->objectType}.pid={$pid}");
        }

        $status = trim($this->getProperty('status'));
        if (!empty($status)) {
            $c->where("{$this->objectType}.status={$status}");
        }


        $c->leftJoin('msProduct', 'msProduct', 'msProduct.id=idImageClose.pid');
        $c->select('msProduct.pagetitle AS pagetitle');

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
        unset($array['similar']);
        $cloud = $this->setCheckbox('cloud');

        $array['images'] = $object->getProducts();

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
